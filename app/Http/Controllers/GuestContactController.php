<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class GuestContactController extends Controller
{
    use ApiResponseTrait;

    public function index(){
        return $this->apiResponse("ok","this is a contact us page");
    }

    public function store(Request $request){
        try{
        $request->validate([
            'email' => 'required|string|email',
            'subject'=>'required|string',
            'description'=>'required|string',
        ]);
        $contact=Contact::create([
            'email' => $request->email,
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
