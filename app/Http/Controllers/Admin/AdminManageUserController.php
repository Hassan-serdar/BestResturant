<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AdminManageUserController extends Controller
{
    // This controller to see the register users and delete a user 
    use ApiResponseTrait;

    public function index(): JsonResponse
    {
        $users = User::paginate(10);
        return $this->apiResponse(
            "Ok",
            "Data retrieved successfully",
            [
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'next_page_url' => $users->nextPageUrl(),
                    'prev_page_url' => $users->previousPageUrl(),
                ],
            ]
        );

    }

    public function show($id): JsonResponse
    {
            $user = User::find($id);
            if (!$user) {
                return $this->notFoundResponse();
            }
            return $this->apiResponse("Ok", "Data retrieved successfully", $user);


    }
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return $this->notFoundResponse();
        }

        $user->delete();

        return $this->deletedResponse();
    }


}
