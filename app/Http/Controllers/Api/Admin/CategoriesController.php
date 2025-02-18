<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $Category = Category::orderBy('id', 'desc')->get();

        return $this->success('Category retrieved successfully.', $Category);
    }

    public function restore($id)
    {
        $Category = Category::onlyTrashed()->find($id);

        if (!$Category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        $Category->restore();

        return $this->success('Category restored successfully!', $Category);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_group_id' => 'required|exists:category_groups,id',
            'name'              => 'required|string|max:200|unique:categories,name,NULL,id,deleted_at,NULL',
            'slug'              => 'required|string|max:200|unique:categories,slug,NULL,id,deleted_at,NULL',
            'description'       => 'nullable|string',
            'icon'              => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'category_group_id.required' => 'The category group id field is required.',
            'category_group_id.exists' => 'The selected category group id is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'name.max' => 'The name may not be greater than 200 characters.',
            'slug.required' => 'The slug field is required.',
            'slug.max' => 'The slug may not be greater than 200 characters.',
            'slug.unique' => 'The slug must be unique.',
            'description.string' => 'The description must be a string.',
            'icon.required' => 'The icon is required.',
            'icon.image' => 'The icon must be an image.',
            'icon.mimes' => 'The icon must be a jpeg, png, jpg, gif, svg, or webp file.',
            'icon.max' => 'The icon must not be larger than 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if a soft-deleted category with the same name or slug exists
        $existingCategory = Category::withTrashed()
            ->where(function ($query) use ($request) {
                $query->where('name', $request->name)
                    ->orWhere('slug', $request->slug);
            })
            ->first();

        if ($existingCategory && $existingCategory->trashed()) {
            // Restore the soft-deleted record
            $existingCategory->restore();

            // Return a response indicating the category was restored
            return response()->json(['message' => 'Category Restored Successfully!', 'data' => $existingCategory], 200);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('icon')) {
            $image = $request->file('icon');

            $imagePath = 'assets/images/categories';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move($imagePath, $imageName);

            $validatedData['icon'] = $imagePath . '/' . $imageName;
        }

        $category = Category::create($validatedData);

        return response()->json(['message' => 'Category Created Successfully!', 'data' => $category], 201);
    }

    public function show($id)
    {
        $category = Category::with(['categoryGroup'])->find($id);

        if (!$category) {
            return $this->error('Product Not Found.', ['error' => 'Product Not Found']);
        }

        $category->categoryGroupName = $category->categoryGroup ? $category->categoryGroup->name : null;

        unset($category->categoryGroup);

        return $this->success('Category Retrieved Successfully!', $category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                'category_group_id' => 'required|exists:category_groups,id',
                'name'              => 'required|string|max:200|unique:categories,name,' . $id,
                'slug'              => 'required|string|max:200|unique:categories,slug,' . $id,
                'description'       => 'nullable|string',
                'icon'        => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ],
            [
                'category_group_id.required' => 'The category group id field is required.',
                'category_group_id.exists' => 'The selected category group id is invalid.',
                'name.required' => 'The name field is required.',
                'name.unique' => 'The name field must be unique.',
                'name.max' => 'The name may not be greater than 200 characters.',
                'slug.required' => 'The slug field is required.',
                'slug.max' => 'The slug may not be greater than 200 characters.',
                'slug.unique' => 'The slug must be unique.',
                'description.string' => 'The description must be a string.',
                'icon.required' => 'The icon field is required',
                'icon.image' => 'The icon must be an image.',
                'icon.mimes' => 'The icon must be a jpeg, png, jpg, gif, svg, or webp file.',
                'icon.max' => 'The icon must not be larger than 2MB.',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imagePath = 'assets/images/categories';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            if ($category->icon && file_exists(public_path($category->icon))) {
                unlink(public_path($category->icon));
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            $validatedData['icon'] = $imagePath . '/' . $imageName;
        }

        $category->update($validatedData);

        return $this->success('Category Updated Successfully!', $category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return $this->error('Category Not Found.', ['error' => 'Category Not Found']);
        }
        $category = Category::findOrFail($id);
        $category->delete();

        return $this->ok('Category Deleted Successfully!');
    }
}
