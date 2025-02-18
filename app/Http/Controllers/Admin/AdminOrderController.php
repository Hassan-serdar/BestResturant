<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AdminOrderController extends Controller
{
    use ApiResponseTrait;
    public function index(): JsonResponse
    {
        $orders = Order::paginate(10);
        return $this->apiResponse(
            "Ok",
            "Data retrieved successfully",
            [
                'data' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                    'next_page_url' => $orders->nextPageUrl(),
                    'prev_page_url' => $orders->previousPageUrl(),
                ],
            ]
        );

    }

    public function update(Request $request, $id)
{
    try{
    $order = Order::find($id);

    if (!$order) {
        return $this->notFoundResponse( 'Order not found');
    }

    $order->status = $request->status;
    $order->save();

    return $this->updatedResponse([$order,'Order status updated successfully']);
} catch (\Exception $e) {
    return $this->serverErrorResponse($e->getMessage());
}
}
}
