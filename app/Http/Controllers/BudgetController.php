<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Order;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budget = Budget::firstOrFail();
        return response()->json($budget, 200);
    }
    public function updateBudget(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $budget = Budget::firstOrFail();

        $orderPrice = $order->initPrice * $order->quantity;
        $profit = ($order->price - $order->initPrice) * $order->quantity;

        $budget->budget -= $orderPrice;
        $budget->profit += $profit;

        $budget->save();

        return response()->json(['message' => 'Budget updated successfully'], 200);
    }
}
