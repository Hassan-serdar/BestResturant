<?php

namespace App\Http\Controllers\user;

use App\Models\Cart;
use App\Models\Menu;
use App\Models\Offer;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function addItem(Request $request,$id)
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $menuItem = Menu::find($id);
        if (!$menuItem) {
        return $this->notFoundResponse();
        }
            $cartItem = new CartItem([
                'food_id' => $menuItem->id,
                'type' => 'menu',
                'product_name' => $menuItem->name,
                'quantity' => $request->quantity,
                'price' => $menuItem->price,
            ]);

            $cart->items()->save($cartItem);
            $cart->calculateTotalPrice();
    
            return $this->createdResponse($cart, 'Item added to cart');   
    }
    public function addOffer(Request $request,$id)
    {
        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $menuItem = Offer::find($id);
        if($request->quantity==0){
            return $this->badRequestResponse("can not add quantity less than 1");
        }
        if (!$menuItem) {
        return $this->notFoundResponse();
        }
            $cartItem = new CartItem([
                'food_id' => $menuItem->id,
                'type' => 'menu',
                'product_name' => $menuItem->name,
                'quantity' => $request->quantity,
                'price' => $menuItem->newprice,
            ]);

            $cart->items()->save($cartItem);
            $cart->calculateTotalPrice();
    
            return $this->createdResponse($cart, 'Item added to cart');   
    }


    public function applyDiscount(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
    
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    
        $discountCode = DiscountCode::where('code', trim($request->discount_code))
            ->where('is_active', true) 
            ->where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->first();
    
        if (!$discountCode) {
            return response()->json(['message' => 'Invalid discount code'], 400);
        }
    
        if ($discountCode->to === 'single_user') {
            if ($discountCode->user_id !== $user->id) {
                return response()->json(['message' => 'You are not allowed to use this discount code'], 403);
            }
        }
    
        $cart->discount = $discountCode->discount;
        $cart->calculateTotalPrice();
    
        return response()->json([
            'message' => 'Discount applied successfully',
            'cart' => $cart,
        ]);
    }    public function getCart()
    {
        $user = Auth::user();
        $cart = Cart::with('items')->where('user_id', $user->id)->first();

        if ($cart) {
            return $this->retrievedResponse($cart, 'Cart retrieved successfully');
        }

        return $this->notFoundResponse('Cart not found');
    }
    public function removeItem($id)
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return $this->notFoundResponse('Cart not found');
        }

        $cartItem = CartItem::where('cart_id', $cart->id)->where('id', $id)->first();
        if (!$cartItem) {
            return $this->notFoundResponse('Item not found in cart');
        }

        $cartItem->delete();
        $cart->calculateTotalPrice();

        return $this->apiResponse('ok', 'Item removed successfully',$cart);
    }
    public function clearCart()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();
        if (!$cart) {
            return $this->notFoundResponse('Cart not found');
        }

        $cart->items()->delete();
        $cart->discount = 0;
        $cart->calculateTotalPrice();

        return $this->apiResponse('ok', 'Item removed successfully',$cart);
    }
    public function confirmOrder()
    {
        $user = Auth::user();
        $cart = Cart::with('items')->where('user_id', $user->id)->first();

        if (!$cart) {
            return $this->notFoundResponse('Cart not found');
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $cart->total_price,
            'discount' => $cart->discount,
            'status' => 'pending',
        ]);

        foreach ($cart->items as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => $cartItem->product_name,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);
        }

        $cart->items()->delete();
        $cart->delete();

        return $this->createdResponse([$order,
            'message' => 'Order confirmed successfully',
        ]);
    }
}
