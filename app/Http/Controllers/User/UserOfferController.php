<?php

namespace App\Http\Controllers\user;

use App\Models\Offer;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;

class UserOfferController extends Controller
{
    // This controller to see offers
    use ApiResponseTrait;
    // see all offers 
    public function index(){
        $offers=Offer::paginate(10);
        return $this->apiResponse(
            "Ok",
            "Data retrieved successfully",
            [
                'data' => $offers->items(),
                'pagination' => [
                    'current_page' => $offers->currentPage(),
                    'per_page' => $offers->perPage(),
                    'total' => $offers->total(),
                    'last_page' => $offers->lastPage(),
                    'next_page_url' => $offers->nextPageUrl(),
                    'prev_page_url' => $offers->previousPageUrl(),
                ],
            ]
        );
    }
    // see a specific item 
    public function show($id){
        {
            $offer = Offer::find($id);
            if (!$offer) {
                return $this->notFoundResponse();
            }
            return $this->apiResponse("Ok", "Data retrieved successfully", $offer);
        }
    
    }
}
