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

    public function updateFirst(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'budget' => 'required|numeric|min:0',
            'profit' => 'required|numeric|min:0',
        ]);

        // Fetch the first budget record
        $budget = Budget::first();

        // Check if a budget record exists
        if (!$budget) {
            return response()->json([
                'message' => 'No budget record found',
            ], 404);
        }

        // Update the budget
        $budget->update($validatedData);

        // Return a response
        return response()->json([
            'message' => 'Budget updated successfully',
            'data' => $budget
        ], 200);
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
