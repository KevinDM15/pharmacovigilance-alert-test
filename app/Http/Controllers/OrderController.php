<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderIndexRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(OrderIndexRequest $request): AnonymousResourceCollection
    {
        $orders = Order::with(['customer', 'items.medication'])
            ->lot($request->validated('lot'))
            ->purchasedBetween($request->startDate(), $request->endDate())
            ->latest('purchase_date')
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function show(Order $order): OrderResource
    {
        $order->load(['customer', 'items.medication']);

        return new OrderResource($order);
    }

    public function export(OrderIndexRequest $request): StreamedResponse
    {
        $orders = Order::with('customer')
            ->lot($request->validated('lot'))
            ->purchasedBetween($request->startDate(), $request->endDate())
            ->latest('purchase_date')
            ->get();

        $filename = 'orders-lot-'.$request->validated('lot').'.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Order ID', 'Customer', 'Email', 'Phone', 'Purchase Date']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $this->escapeCsvField($order->customer->name),
                    $this->escapeCsvField($order->customer->email),
                    $this->escapeCsvField($order->customer->phone),
                    $order->purchase_date->toDateString(),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function escapeCsvField(?string $value): ?string
    {
        if ($value !== null && preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'".$value;
        }

        return $value;
    }
}
