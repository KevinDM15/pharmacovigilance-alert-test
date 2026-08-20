<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendAlertRequest;
use App\Mail\LotRecallAlert;
use App\Models\Alert;
use App\Models\Medication;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class AlertController extends Controller
{
    public function send(SendAlertRequest $request): JsonResponse
    {
        $medication = Medication::lot($request->validated('lot'))->firstOrFail();

        $orders = Order::with('customer')
            ->whereIn('id', $request->validated('order_ids'))
            ->lot($medication->lot_number)
            ->get();

        foreach ($orders as $order) {
            Alert::create([
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'sent_at' => now(),
            ]);

            Mail::to($order->customer)->send(
                new LotRecallAlert($order->customer, $order, $medication),
            );
        }

        return response()->json([
            'message' => "Alert sent to {$orders->count()} customer(s).",
        ]);
    }
}
