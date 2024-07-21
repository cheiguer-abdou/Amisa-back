<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function order(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id', // Validate product ID
            'quantity' => 'required|integer|min:1', // Ensure quantity is a positive integer
        ]);

        $productId = $request->product_id;

        $product = Product::findOrFail($productId);

        if ($product->quantity >= $request->quantity) {
            // Decrease product quantity
            $product->decrement('quantity', $request->quantity);

            // Calculate profit
            $profit = ($product->price - $product->initPrice) * $request->quantity;

            // Create order record
            $order = Order::create([
                'client_id' => $request->client_id,
                'product_id' => $productId,
                'quantity' => $request->quantity,
                // Add additional fields as needed
            ]);

            // Update budget
            $budget = Budget::firstOrFail();
            $budget->budget += $product->price * $request->quantity;
            $budget->profit += $profit; // Adding profit to the budget
            $budget->save();

            return response()->json(['data' => $order], 200);
        } else {
            return response()->json(['error' => 'Ordered quantity exceeds available quantity'], 400);
        }
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1); // Default to page 1 if not provided
        $size = $request->input('size', 5); // Default to 5 rows per page if not provided

        $orders = Order::with('product', 'client')
            ->orderBy('created_at', 'desc')
            ->paginate($size, ['*'], 'page', $page);
        return response()->json($orders, 200);
    }

    public function update(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            // Find the order by ID
            $order = Order::findOrFail($id);

            // Update the order
            $order->client_id = $request->client_id;
            $order->product_id = $request->product_id;
            $order->quantity = $request->quantity;
            $order->save();

            return response()->json(['message' => 'Order updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update order', 'error' => $e->getMessage()], 500);
        }
    }


    public function searchOrders(Request $request)
    {
        $keyword = $request->input('keyword');

        $orders = Order::with('client', 'product')
            ->where(function ($query) use ($keyword) {
                $query->whereHas('client', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%$keyword%");
                })
                    ->orWhereHas('product', function ($query) use ($keyword) {
                        $query->where('name', 'like', "%$keyword%");
                    });
            })
            ->paginate(5);

        return response()->json($orders, 200);
    }

    public function salesPerYear()
    {
        $currentYear = now()->year;
        $lastYear = $currentYear - 1;

        $salesPerYear = Order::selectRaw('orders.product_id, YEAR(orders.created_at) as year, MONTH(orders.created_at) as month, SUM(products.quantity * products.price) as total_revenue')
            ->join('products', 'orders.product_id', '=', 'products.id') // Join with products table to get the price
            ->whereIn(DB::raw('YEAR(orders.created_at)'), [$currentYear, $lastYear])
            ->groupBy('orders.product_id', 'year', 'month')
            ->get();

        return response()->json($salesPerYear);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}
