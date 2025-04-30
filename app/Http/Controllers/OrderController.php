<?php
namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Models\AffiliationPayment;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
  public function index()
{
    $orders = Order::with('user:id,last_name', 'pyments')->get();

    $ordersWithFilteredData = $orders->map(function ($order) {
        $orderArray = $order->toArray();
        $orderArray['user_last_name'] = $order->user ? $order->user->last_name : null;

        // Transform pyments to include only due and complated
        $orderArray['pyments'] = $order->pyments->map(function ($payment) {
            return [
                'id'=>$payment->id,
                'due' => $payment->due,
                'complated' => $payment->complated,
            ];
        });

        return $orderArray;
    });

    return response()->json($ordersWithFilteredData);
}

 

public function store(OrderRequest $request)
{
    $validated = $request->validated();

    // ✅ Normalize product_id
    if (isset($validated['product_id'])) {
        if (is_string($validated['product_id'])) {
            $decoded = json_decode($validated['product_id'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['product_id'] = $decoded;
            } else {
                $validated['product_id'] = explode(',', $validated['product_id']);
            }
        }

        $validated['product_id'] = array_map('intval', $validated['product_id']);
    }

    // ✅ Normalize item
   

    // ✅ Normalize quantity
    if (isset($validated['quantity'])) {
        if (is_string($validated['quantity'])) {
            $decoded = json_decode($validated['quantity'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['quantity'] = $decoded;
            } else {
                $validated['quantity'] = explode(',', $validated['quantity']);
            }
        }

        $validated['quantity'] = array_map('intval', $validated['quantity']);
    }

    DB::beginTransaction();

    try {
        foreach ($validated['product_id'] as $index => $stockId) {
            $quantity = $validated['quantity'][$index] ?? 0;

            $stock = Stock::where('id', $stockId)->lockForUpdate()->first();

            if (!$stock) {
                DB::rollBack();
                return response()->json(["message" => "Stock entry with ID $stockId not found"], 400);
            }

            if (!is_numeric($stock->quantity) || $stock->quantity < $quantity) {
                DB::rollBack();
                return response()->json([
                    "message" => "Not enough stock for ID: $stockId",
                    "available_stock" => $stock->quantity
                ], 400);
            }

            $stock->decrement('quantity', $quantity);
            $stock->touch();
        }

        $order = Order::create($validated);
       if (isset($validated['due'])) {
    AffiliationPayment::create([
        'due' => $validated['due'],
        'user_id' => $validated['user_id'],
        'complated' => 1,
        'order_id' => $order->id,
    ]);
}

        DB::commit();

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            "message" => "An error occurred",
            "error" => $e->getMessage()
        ], 500);
    }
}




    public function show(Order $order)
    {
        return response()->json($order);
    }

    public function update(OrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }

  public function destroy( $order)
{
    $order = Order::find($order);
    if (!$order) {
        return response()->json(['message' => 'Order not found'], 404);
    }

    try {
        $order->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Failed to delete order', 'error' => $e->getMessage()], 500);
    }
}


public function orderForUser($id)
{
    $orders = Order::with('user:id,last_name', 'pyments')
        ->where('user_id', $id)
        ->get();

    $ordersWithFilteredData = $orders->map(function ($order) {
        $orderArray = $order->toArray();
        $orderArray['user_last_name'] = $order->user ? $order->user->last_name : null;

        // Keep only due and complated in pyments
        $orderArray['pyments'] = $order->pyments->map(function ($payment) {
            return [
                'due' => $payment->due,
                'complated' => $payment->complated,
            ];
        });

        return $orderArray;
    });

    return response()->json($ordersWithFilteredData);
}

    
    
     public function updateStatus(Request $request,  $id)
    {
        $order = Order::find($id);
        $order->update($request->all());
        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }
    
public function rejectOrder( Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id'
    ]);

    DB::beginTransaction();

    try {
        // جلب الطلب
        $order = Order::find($validated['order_id']);

        if (!$order) {
            DB::rollBack();
            return response()->json(["message" => "Order not found"], 404);
        }

        // التحقق مما إذا كانت البيانات JSON أو مصفوفة
        $productIds = is_string($order->product_id) ? json_decode($order->product_id, true) : $order->product_id;
        $quantities = is_string($order->quantity) ? json_decode($order->quantity, true) : $order->quantity;
        if (!is_array($productIds) || !is_array($quantities)) {
            DB::rollBack();
            return response()->json(["message" => "Invalid order data format"], 400);
        }

        foreach ($productIds as $index => $stockId) {
            $quantity = intval($quantities[$index]);

            $stock = Stock::where('id', $stockId)->lockForUpdate()->first();

            if ($stock) {
                $stock->increment('quantity', $quantity);
                $stock->touch();
            }
        }

        // حذف الطلب بعد استرجاع المخزون

        DB::commit();

        return response()->json(['message' => 'Order reversed successfully'], 200);
    
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(["message" => "An error occurred", "error" => $e->getMessage()], 500);
    }
}


public function getPymentToUser($id){
    $data = AffiliationPayment::where('user_id',$id)->get();
    return response()->json(['message' => 'All Affiliation Payment successfully', 'order' => $data]);
}




    
}
