<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;
use App\Models\DealCategory;

class DealCategoryController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $dealCategory = DealCategory::orderBy('id', 'desc')->get();

        return $this->success('Deal Category Retrieved successfully.', $dealCategory);
    }

    public function restore($id)
    {
        $dealCategory = DealCategory::onlyTrashed()->find($id);

        if (!$dealCategory) {
            return $this->error('Deal Category Not Found.', ['error' => 'Deal Category Not Found']);
        }
        $dealCategory->restore();

        return $this->success('Deal Category Restored Successfully!', $dealCategory);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|unique:deal_categories,name',
            'slug'        => 'required|string|unique:deal_categories,slug',
            'description' => 'nullable|string',
            'image'        => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug field must be unique.',
            'image.required' => 'The image is required.',
            'image.image' => 'The image must be an image.',
            'image.mimes' => 'The image must be a jpeg, png, jpg, gif, svg or webp file.',
            'image.max' => 'The image must not be larger than 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'assets/images/deal_categories';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            $validatedData['image_path'] = $imagePath . '/' . $imageName;
        }

        $dealCategory = DealCategory::create($validatedData);

        return $this->success('Deal Category Created Successfully!', $dealCategory);
    }

    public function update(Request $request, $id)
    {
        $dealCategory = DealCategory::find($id);

        if (!$dealCategory) {
            return $this->error('Deal Category Not Found.', ['error' => 'Deal Category Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|unique:deal_categories,name,' . $id,
            'slug' => 'sometimes|required|string|unique:deal_categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'slug.required' => 'The slug field is required.',
            'slug.unique' => 'The slug must be unique.',
            'image.image' => 'The image must be an image.',
            'image.mimes' => 'The image must be a jpeg, png, jpg, gif, svg or webp file.',
            'image.max' => 'The image must not be larger than 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'assets/images/deal_categories';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            if ($dealCategory->image_path && file_exists(public_path($dealCategory->image_path))) {
                unlink(public_path($dealCategory->image_path));
            }

            $validatedData['image_path'] = $imagePath . '/' . $imageName;
        }

        $dealCategory->update($validatedData);

        return $this->success('Deal Category Updated Successfully!', $dealCategory);
    }


    public function show($id)
    {

        $dealCategory = DealCategory::find($id);


        if (!$dealCategory) {
            return $this->error('Deal Category Not Found.', ['error' => 'Deal Category Not Found']);
        }

        return $this->success('Deal Category Retrived Succesfully!', $dealCategory);
    }

    public function delete($id)
    {
        $dealCategory = DealCategory::find($id);

        if (!$dealCategory) {
            return $this->error('Deal Category Not Found.', ['error' => 'Deal Category Not Found']);
        }

        $dealCategory->delete();
        return $this->ok('Deal Category Deleted Successfully!');
    }
}
