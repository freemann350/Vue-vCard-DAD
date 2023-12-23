<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Vcard;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return CategoryResource::collection(Category::query()->where("vcard", $request->user()['username'])->orderBy('id')->get());
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function store(StoreUpdateCategoryRequest $request)
    {
        $newCategory = Category::create($request->validated());
        return new CategoryResource($newCategory);
    }

    public function update(StoreUpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        $counter = Transaction::where('category_id','=',$category->id)->count();
        if ($counter == 0) {
            $category->forceDelete();
        } else {
            $category->delete();
        }
        return new CategoryResource($category);
    }

    public function showVcardTotalCategoriesForVCard(Request $request, Vcard $vcard)
    {
        $totalCategories = Category::where('vcard', $vcard->phone_number)->count();

        return $totalCategories;
    }
}
