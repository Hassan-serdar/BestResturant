<?php 
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:8',
            ]);

            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->createdResponse($user, 'User registered successfully');
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
                'password' => 'required|string|min:8',
            ]);

            if (!Auth::guard('web')->attempt($request->only('email', 'password'))) {
                return $this->unauthorizedResponse('Invalid login details');
            }

            $user = Auth::guard('web')->user();
            $user1 = User::where('email', $request->email)->first();
            $token = $user1->createToken('authToken')->plainTextToken;

            return $this->retrievedResponse(['user' => $user, 'token' => $token], 'User logged in successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->deletedResponse('User logged out successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function profile(Request $request)
    {
        try {
            return $this->retrievedResponse($request->user(), 'User profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
}