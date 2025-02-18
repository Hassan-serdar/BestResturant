<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class MainController extends Controller
{
    use ApiResponseTrait;
    
    public function index(){
        return $this->apiResponse("ok","This is home page response");
    }

    public function about(){
        return $this->apiResponse("ok","This is about us page response");
    }
}

