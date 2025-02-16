<?php

namespace App\Http\Controllers\admin;

use App\Models\DiscountCode;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;

class AdminDiscountCodeController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $code = DiscountCode::paginate(10);
        return $this->apiResponse(
            "Ok",
            "Data retrieved successfully",
            [
                'data' => $code->items(),
                'pagination' => [
                    'current_page' => $code->currentPage(),
                    'per_page' => $code->perPage(),
                    'total' => $code->total(),
                    'last_page' => $code->lastPage(),
                    'next_page_url' => $code->nextPageUrl(),
                    'prev_page_url' => $code->previousPageUrl(),
                ],
            ]
        );
    }

    public function store(Request $request)
    {
        try{
        $request->validate([
            'code' => 'required|unique:discount_codes',
            'discount' => 'required|numeric',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'to' => 'required|in:all,single_user', 
            'user_id' => 'nullable|exists:users,id',
        ]);

        if ($request->to === 'single_user' && !$request->user_id) {
            return $this->badRequestResponse('error','User ID is required for single user scope',400);
        }

        $discountCode = DiscountCode::create($request->all());

        return $this->createdResponse($discountCode, "Code created successfully");
    }catch (\Illuminate\Validation\ValidationException $e) {
        return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
    } catch (\Exception $e) {
        return $this->serverErrorResponse($e->getMessage());
    }
}

    public function show($id)
    {
        $code = DiscountCode::find($id);
        if (!$code) {
            return $this->notFoundResponse();
        }
        return $this->apiResponse("Ok", "Data retrieved successfully", $code);
        

    }

    public function update(Request $request,$id)
    {
        $discountCode = DiscountCode::find($id);
        try{
        $request->validate([
            'code' => 'sometimes|unique:discount_codes,code,' . $discountCode->id,
            'discount' => 'sometimes|numeric',
            'valid_from' => 'sometimes|date',
            'valid_to' => 'sometimes|date|after:valid_from',
            'is_active' => 'sometimes|boolean',
            'to' => 'sometimes|in:all,single_user',
            'user_id' => 'nullable|exists:users,id',
        ]);
        $discountCode = DiscountCode::find($id);
        if (!$discountCode) {
            return $this->notFoundResponse();
        }

        $discountCode->update($request->all());

        return $this->updatedResponse($discountCode,'DiscountCode updated successfully');
    }catch (\Illuminate\Validation\ValidationException $e) {
        return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
    } catch (\Exception $e) {
        return $this->serverErrorResponse($e->getMessage());
    }
}

    public function destroy($id)
    {
        $discountCode = DiscountCode::find($id);
        if (!$discountCode) {
            return $this->notFoundResponse();
        }
        $discountCode->delete();

        return $this->deletedResponse('DiscountCode deleted successfully');
    }
}