<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Contact;

class AdminContactusController extends Controller
{
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $contact = Contact::paginate(10);
        return $this->apiResponse(
            "Ok",
            "Data retrieved successfully",
            [
                'data' => $contact->items(),
                'pagination' => [
                    'current_page' => $contact->currentPage(),
                    'per_page' => $contact->perPage(),
                    'total' => $contact->total(),
                    'last_page' => $contact->lastPage(),
                    'next_page_url' => $contact->nextPageUrl(),
                    'prev_page_url' => $contact->previousPageUrl(),
                ],
            ]
        );

    }

}
