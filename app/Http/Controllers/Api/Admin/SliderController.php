<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $sliders = Slider::with('country:id,country_name') 
                         ->orderBy('id', 'desc')
                         ->get();
    
        return $this->success('Sliders Retrieved Successfully!', $sliders);
    }
    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'order' => 'required|integer',
            'country_id' => 'nullable|exists:countries,id',
        ], [
            'image.required' => 'The image field is required.',
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
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'assets/images/sliders';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);

            $validatedData['image_path'] = $imagePath . "/" . $imageName;
        }

        $validatedData['country_id'] = $request->input('country_id');

        $slider = Slider::create($validatedData);
        // dd($slider);

        return $this->success('Slider Created Successfully!', $slider);
    }

    public function show(string $id)
    {
        $slider = Slider::find($id);
        if (!$slider) {
            return $this->error('Slider Not Found.', ['error' => 'Slider Not Found']);
        }
        return $this->success('Slider Retrived Succesfully!', $slider);
    }

    public function update(Request $request, string $id)
    {
        $slider = Slider::find($id);

        if (!$slider) {
            return $this->error('Slider Not Found.', ['error' => 'Slider Not Found']);
        }

        $validator = Validator::make($request->all(), [
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'order' => 'required|integer',
            'country_id' => 'nullable|exists:countries,id',
        ], [
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
            if ($slider->image_path && file_exists($slider->image_path)) {
                unlink($slider->image_path);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'assets/images/sliders';

            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }

            $image->move($imagePath, $imageName);

            $validatedData['image_path'] = $imagePath . "/" . $imageName;
        }

        $validatedData['country_id'] = $request->input('country_id');

        $slider->update($validatedData);

        return $this->success('Slider Updated Successfully!', $slider);
    }

    public function destroy(string $id)
    {
        $slider = Slider::findOrFail($id);
        $slider->delete();
        return $this->ok('Slider Deleted Successfully!');
    }
}
