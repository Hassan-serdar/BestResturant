<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserOrderController extends Controller
{
    use ApiResponseTrait;

    public function myOrders()
    {
        try{
        $user = Auth::user();
        $orders = Order::with('items')->where('user_id', $user->id)->get();

        return $this->apiResponse(['ok',
        'orders retrieved successfully',
        'orders' => $orders,
        ]);
    }catch (\Illuminate\Validation\ValidationException $e) {
        return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
    } catch (\Exception $e) {
        return $this->serverErrorResponse($e->getMessage());
    }
}
}