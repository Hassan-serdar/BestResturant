<?php

namespace App\Http\Controllers\user;

use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserDiscountCodeController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        try{
        $user = Auth::user();

        $discountCodesForAll = DiscountCode::where('to','all')
        ->where('is_active', true)
        ->where('valid_from', '<=', now())
        ->where('valid_to', '>=', now())
        ->get();
    
        $discountCodesForUser = DiscountCode::where('to', 'single_user')
        ->where('user_id', $user->id)
        ->where('is_active', true)
        ->where('valid_from', '<=', now())
        ->where('valid_to', '>=', now())
        ->get();
    
    $discountCodes = $discountCodesForAll->merge($discountCodesForUser);
    
        return $this->apiResponse("ok", 'Discount codes retrieved successfully',$discountCodes);
    }catch (\Illuminate\Validation\ValidationException $e) {
        return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
    } catch (\Exception $e) {
        return $this->serverErrorResponse($e->getMessage());
    }
}
}