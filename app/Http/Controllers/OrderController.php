<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderIndexRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
}
