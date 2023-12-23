<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateDefaultCategoryRequest;
use App\Http\Resources\DefaultCategoryResource;
use App\Models\DefaultCategory;
use Illuminate\Http\Request;

class DefaultCategoryController extends Controller
{
    public function index()
    {
        return DefaultCategoryResource::collection(DefaultCategory::all());
    }

    public function show(DefaultCategory $defaultcategory)
    {
        return new DefaultCategoryResource($defaultcategory);
    }

    public function store(StoreUpdateDefaultCategoryRequest $request)
    {
        $newDefaultCategory = DefaultCategory::create($request->validated());
        return new DefaultCategoryResource($newDefaultCategory);
    }

    public function update(StoreUpdateDefaultCategoryRequest $request, DefaultCategory $defaultcategory)
    {
        $defaultcategory->update($request->validated());
        return new DefaultCategoryResource($defaultcategory);
    }

    public function destroy(DefaultCategory $defaultcategory)
    {
        $defaultcategory->delete();
        return new DefaultCategoryResource($defaultcategory);
    }
}
