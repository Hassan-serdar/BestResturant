<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|string|email|unique:admins',
                'password' => 'required|string|min:6',
            ]);

            $admin = Admin::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->createdResponse($admin, 'Admin registered successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::guard('admin')->attempt($request->only('email', 'password'))) {
                return $this->unauthorizedResponse('Invalid login details');
            }

            $admin = Auth::guard('admin-api')->user();
            $admin1 = Admin::where('email', $request->email)->first();
            $token = $admin1->createToken('admin-token')->plainTextToken;

            return $this->retrievedResponse(['admin' => $admin, 'token' => $token], 'Admin logged in successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return $this->deletedResponse('Admin logged out successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function profile(Request $request)
    {
        try {
            return $this->retrievedResponse($request->user(), 'Admin profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}