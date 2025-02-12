<?php

namespace App\Http\Controllers\User;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserMenuController extends Controller
{
    use ApiResponseTrait;

    /**

    */
    public function index(): JsonResponse
    {
        $menus = Menu::paginate(10);
        return $this->apiResponse(
            "Ok",
            "Data retrieved successfully",
            [
                'data' => $menus->items(),
                'pagination' => [
                    'current_page' => $menus->currentPage(),
                    'per_page' => $menus->perPage(),
                    'total' => $menus->total(),
                    'last_page' => $menus->lastPage(),
                    'next_page_url' => $menus->nextPageUrl(),
                    'prev_page_url' => $menus->previousPageUrl(),
                ],
            ]
        );
    }

    /**
    
     */
    public function show($id): JsonResponse
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return $this->notFoundResponse();
        }
        return $this->apiResponse("Ok", "Data retrieved successfully", $menu);
    }

    public function showcategory($categoryname): JsonResponse
    {
        try {
            $menus = Menu::where('category', $categoryname)->get();
    
            if ($menus->isEmpty()) {
                return $this->notFoundResponse("No items found for category: $categoryname");
            }
    
            return $this->apiResponse("Ok", "Data retrieved successfully", $menus);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    
}