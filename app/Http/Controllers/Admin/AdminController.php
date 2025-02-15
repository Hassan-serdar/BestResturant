<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // This controller for login ,register , logout and show an admin profile
    use ApiResponseTrait,HasApiTokens;

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
        } catch (\Illuminate\Validation\ValidationException $e) { // if there a Validation error 
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

            /* @var Admmin $admin **/ $admin = Auth::guard('admin')->user();
            $admin1=Admin::where('email',$request->email)->first();
            $token = $admin1->createToken("Admin-Token")->plainTextToken;

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
            $user = $request->user();
            if($user){
            return $this->retrievedResponse($user, 'Admin profile retrieved successfully');
            }else {
                return $this->serverErrorResponse('Admin not authenticated');
            }
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }    
    }
    public function editprofile(Request $request){
        try {
            $validated = $request->validate([
                'oldpassword' => 'required|string|min:8', 
                'newpassword' => 'required|string|min:8|confirmed', 
            ]);
    
            $user = $request->user();
            
            if (!Hash::check($validated['oldpassword'], $user->password)) {
                return $this->validationErrorResponse(['oldpassword' => ['The old password is incorrect.']]);
            }
            if($validated['oldpassword'] === $validated['newpassword']){
                return $this->validationErrorResponse([],'new password can not be the same of old password');
            }
            $user->update([
                'password' => Hash::make($validated['newpassword']),
            ]);
            return $this->updatedResponse($user, "Information updated successfully");
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

}