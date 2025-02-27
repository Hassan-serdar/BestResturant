<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminMenuController extends Controller
{
    // This controller to edit , create , update , delete from the menu
    use ApiResponseTrait;
    /**
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'description' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'category' => 'required|in:Eastern_food,Western_food,Desserts,Juices', 
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName(); 
                $image->storeAs('public/images', $imageName); 
                $validated['image_name'] = $imageName; 
            }
        
            $menu = Menu::create($validated);

            return $this->createdResponse($menu);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    public function edit($id): JsonResponse
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return $this->notFoundResponse();
        }
        return $this->apiResponse("Ok", "Data retrieved successfully", $menu);
    }

    /**
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'description' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'category' => 'required|in:Eastern_food,Western_food,Desserts,Juices', 
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $menu = Menu::find($id);
            if (!$menu) {
                return $this->notFoundResponse();
            }

            if ($request->hasFile('image')) {
                if ($menu->image_name && Storage::exists('public/images/' . $menu->image_name)) {
                    Storage::delete('public/images' . $menu->image_name);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName(); 
                $image->storeAs('public/images', $imageName); 
                $validated['image_name'] = $imageName; 
            }

            $menu->update($validated);

            return $this->updatedResponse($menu);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
    }

    /**
     */
    public function destroy($id): JsonResponse
    {
        $menu = Menu::find($id);
        if (!$menu) {
            return $this->notFoundResponse();
        }

        if ($menu->image_name && Storage::exists('public/images/' . $menu->image_name)) {
            Storage::delete('public/images/' . $menu->image_name);
        }

        $menu->delete();

        return $this->deletedResponse();
    }
}