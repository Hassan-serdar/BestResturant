<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Contact;

class UserContactController extends Controller
{
    use ApiResponseTrait;
    public function index(){
        return $this->apiResponse("ok","this is a contact us page");
    }

    public function store(Request $request){
        try{
        $request->validate([
            'subject'=>'required|string',
            'description'=>'required|string',
        ]);
        $user=$request->user();
        $contact=Contact::create([
            'email' => $user->email,
            'subject'=> $request->subject,
            'description'=> $request->description,

        ]);
        return $this->createdResponse($contact,"message sent successfully");
    }catch (\Illuminate\Validation\ValidationException $e) {
        return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
    } catch (\Exception $e) {
        return $this->serverErrorResponse($e->getMessage());
    }
}
}
