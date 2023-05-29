<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Gate;
use Response as GlobalResponse;


class OrderController extends Controller
{
    public function index()
    {
        Gate::authorize('view', 'orders');
        $order = Order::paginate();

        return OrderResource::collection($order);
    }

    public function show($id)
    {
        Gate::authorize('view', 'orders');
        $order = Order::find($id);

        return new OrderResource($order);
    }

    public function export()
    {
        Gate::authorize('view', 'orders');
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment;filename=orders.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () {

            $orders = Order::all();
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID', 'Name', 'Email', 'Product Title', 'Price', 'Quantity']);

            foreach ($orders as $order) {
                fputcsv($file, [$order->id, $order->name, $order->email, '', '', '']);
                foreach ($order->orderItems as $item) {
                    fputcsv($file, ['', '', '', $item->product_title, $item->price, $item->quantity]);
                }
            }
            fclose($file);
        };

        return GlobalResponse::stream($callback, 200, $headers);
    }
}
