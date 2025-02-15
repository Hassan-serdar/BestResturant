<?php 
namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use GuzzleHttp\Client;


class UserController extends Controller
{
        // This controller for login ,register , logout and show an user profile

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
            event(new Registered($user));
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

          /* @var User $user **/  
            $user = Auth::guard('web')->user();
            $user1=User::where('email',$request->email)->first();
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
            $user = $request->user();
            if ($user) {
                $user->tokens()->delete();
                return $this->deletedResponse('User logged out successfully');
            } else {
                return $this->serverErrorResponse('User not authenticated');
            }
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    

    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            if($user){
            return $this->retrievedResponse($user, 'User profile retrieved successfully');
            }else {
                return $this->serverErrorResponse('User not authenticated');
            }
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }        
    public function editprofile(Request $request){
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|string|email|unique:users,email,' . $request->user()->id, // To ensure that the current user's email is excluded
                'oldpassword' => 'required|string|min:8', 
                'newpassword' => 'nullable|string|min:8|confirmed', 
            ]);
    
            $user = $request->user();
    
            if (!Hash::check($validated['oldpassword'], $user->password)) {
                return $this->validationErrorResponse(['oldpassword' => ['The old password is incorrect.']]);
            }
    
                $user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                ]);
    
            if (!empty($validated['newpassword'])) {
                $user->update([
                    'password' => Hash::make($validated['newpassword']),
                ]);
            }
    
            return $this->updatedResponse($user, "Information updated successfully");
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }
    }