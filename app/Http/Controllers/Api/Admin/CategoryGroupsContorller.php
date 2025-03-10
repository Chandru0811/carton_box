<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;

class CategoryGroupsContorller extends Controller
{
    use ApiResponses;

    public function index()
    {
        $categoryGroup = CategoryGroup::with('country:id,country_name')
            ->orderBy('id', 'desc')
            ->get();

        return $this->success('Category Groups Retrieved successfully.', $categoryGroup);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:200|unique:category_groups,name,NULL,id,deleted_at,NULL',
            'slug'        => 'required|string|max:200|unique:category_groups,slug,NULL,id,deleted_at,NULL',
            'description' => 'required|string',
            'icon'        => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'order'       => 'required|integer',
            'country_id' => 'nullable|exists:countries,id',
        ], [
            'name.unique' => 'The name field must be unique.',
            'name.required' => 'The name field is required.',
            'slug.unique' => 'The slug field must be unique.',
            'slug.required' => 'The slug field is required.',
            'description.required' => 'The description field is required.',
            'image.image' => 'The image field must be an image.',
            'image.mimes' => 'The image must be a jpeg, png, jpg, gif, svg, or webp file.',
            'image.max' => 'The image must not be larger than 2MB.',
            'order.required' => 'The order field is required.',
            'order.integer' => 'The order field must be an integer.',
            'country_id.exists' => 'The selected country does not exist in our records.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'assets/images/category_groups';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            $validatedData['image_path'] = $imagePath . '/' . $imageName;
        }

        $validatedData['country_id'] = $request->input('country_id');

        $categoryGroup = CategoryGroup::create($validatedData);

        return $this->success('Category Group Created Successfully!', $categoryGroup);
    }

    public function show($id)
    {

        $categoryGroup = CategoryGroup::find($id);


        if (!$categoryGroup) {
            return $this->error('Category Group Not Found.', ['error' => 'Category Group Not Found']);
        }

        return $this->success('Category Group Retrived Succesfully!', $categoryGroup);
    }

    public function update(Request $request, $id)
    {
        $categoryGroup = CategoryGroup::find($id);

        if (!$categoryGroup) {
            return response()->json(['error' => 'Category Group not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:200|unique:category_groups,name,' . $id,
            'slug'        => 'required|string|max:200|unique:category_groups,slug,' . $id,
            'description' => 'required|string',
            'icon'        => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'order'       => 'required|integer',
            'country_id' => 'nullable|exists:countries,id',
        ], [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'slug.required' => 'The slug field is required.',
            'description.required' => 'The description field is required.',
            'image.image' => 'The image field must be an image.',
            'image.mimes' => 'The image must be a jpeg, png, jpg, gif, svg, or webp file.',
            'image.max' => 'The image must not be larger than 2MB.',
            'order.required' => 'The order field is required.',
            'order.integer' => 'The order field must be an integer.',
            'country_id.exists' => 'The selected country does not exist in our records.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = 'assets/images/category_groups';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            if ($categoryGroup->image_path && file_exists(public_path($categoryGroup->image_path))) {
                unlink(public_path($categoryGroup->image_path));
            }

            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            $validatedData['image_path'] = $imagePath . '/' . $imageName;
        }

        $validatedData['country_id'] = $request->input('country_id');

        $categoryGroup->update($validatedData);

        return $this->success('Category Group Updated Successfully!', $categoryGroup);
    }

    public function delete($id)
    {
        $categoryGroup = CategoryGroup::find($id);

        if (!$categoryGroup) {
            return $this->error('Category Group Not Found.', ['error' => 'Category Group Not Found']);
        }

        $categoryGroup->delete();

        return $this->ok('Category Group Deleted Successfully!');
    }
}
