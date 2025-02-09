<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Events\Validated;
use Symfony\Component\HttpFoundation\JsonResponse;

class MenuController extends Controller
{
    use ApiResponseTrait;
    public function index(): JsonResponse
    {
        $menus = Menu::all();
        return $this->apiResponse("Ok","Data get successfully",$menus);
    }
    public function show($id): JsonResponse
    {
        $menus = Menu::find($id);
        if(!$menus){

            return $this->notFoundResponse();

        }
        return $this->apiResponse("Ok","Data get successfully",$menus);
    }

    public function store(Request $request){
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'description' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
            ]);
    
            $menu = Menu::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => strval($validated['price']),
            ]);
    
            return $this->createdResponse($menu);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e->getMessage());
        }
        
    }

    public function edit($id){
        $menus = Menu::find($id);
        if(!$menus){
            return $this->notFoundResponse();
        }
        return $this->retrievedResponse($menus);
    }

    public function update(Request $request,$id){
      try{
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $menus = Menu::find($id);
        if(!$menus){
            return $this->notFoundResponse();
        }
        $menus->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => strval($validated['price']),
        ]);
        return $this->updatedResponse($menus);
    }catch (\Illuminate\Validation\ValidationException $e) {
        return $this->validationErrorResponse($e->errors(), "Validation errors occurred");
    } catch (\Exception $e) {
        return $this->serverErrorResponse($e->getMessage());
    }
}

    public function destroy($id){
    $menus = Menu::find($id);
        if(!$menus){
            return $this->notFoundResponse();
        }
        $menus->destroy($id);
        return $this->deletedResponse();
    }
}

