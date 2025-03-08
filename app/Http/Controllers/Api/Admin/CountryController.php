<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    use ApiResponses;
    public function index()
    {
        $Countries = Country::orderBy('id', 'desc')->get();
        return $this->success('Countries Retrieved Successfully!', $Countries);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_name' => 'required|string|max:255',
            'flag' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'currency_symbol' => 'nullable|string|max:10',
            'currency_code' => 'nullable|string|max:10',
            'social_links' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'color_code' => 'nullable|string|max:10',
            'country_code' => 'required|string|max:10',
            'phone_number_code' => 'required|string',
        ], [
            'flag.required' => 'The flag image is required.',
            'flag.image' => 'The flag must be an image file.',
            'flag.mimes' => 'The flag must be a jpeg, png, jpg, gif, svg, or webp file.',
            'flag.max' => 'The flag image must not exceed 2MB.',
            'country_name.required' => 'The country name is required.',
            'country_code.required' => 'The country code is required.',
            'country_code.unique' => 'The country code must be unique.',
            'phone_number_code.required' => 'The phone number code is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('flag')) {
            $flag = $request->file('flag');
            $flagName = time() . '_' . $flag->getClientOriginalName();
            $flagPath = 'assets/images/countries';

            if (!file_exists($flagPath)) {
                mkdir($flagPath, 0755, true);
            }

            $flag->move($flagPath, $flagName);

            $validatedData['image_path'] = $flagPath . "/" . $flagName;
        }

        $country = Country::create($validatedData);

        return response()->json([
            'message' => 'Country created successfully!',
            'country' => $country
        ], 201);
    }

    public function show(string $id)
    {
        $Countries = Country::find($id);
        if (!$Countries) {
            return $this->error('Countries Not Found.', ['error' => 'Countries Not Found']);
        }
        return $this->success('Countries Retrived Succesfully!', $Countries);
    }

    public function update(Request $request, string $id)
    {
        $country = Country::find($id);
        if (!$country) {
            return $this->error('Country Not Found.', ['error' => 'Country Not Found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'country_name' => 'sometimes|required|string|max:255',
            'flag' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'currency_symbol' => 'nullable|string|max:10',
            'currency_code' => 'nullable|string|max:10',
            'social_links' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'color_code' => 'nullable|string|max:10',
            'country_code' => 'required|string|max:10',
            'phone_number_code' => 'required|string',
        ], [
            'flag.image' => 'The flag must be an image file.',
            'flag.mimes' => 'The flag must be a jpeg, png, jpg, gif, svg, or webp file.',
            'flag.max' => 'The flag image must not exceed 2MB.',
            'country_name.required' => 'The country name is required.',
            'country_code.required' => 'The country code is required.',
            'country_code.unique' => 'The country code must be unique.',
            'phone_number_code.required' => 'The phone number code is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();


        if ($request->hasFile('flag')) {
            if ($country->flag_path && file_exists($country->flag_path)) {
                unlink($country->flag_path);
            }

            $flag = $request->file('flag');
            $flagName = time() . '_' . $flag->getClientOriginalName();
            $flagPath = 'assets/images/countries';

            if (!file_exists($flagPath)) {
                mkdir($flagPath, 0755, true);
            }

            $flag->move($flagPath, $flagName);

            $validatedData['flag_path'] = $flagPath . "/" . $flagName;
        }


        $country->update($validatedData);

        return response()->json([
            'message' => 'Country updated successfully!',
            'country' => $country
        ], 200);
    }


    public function destroy(string $id)
    {
        $country = Country::findOrFail($id);
        $country->delete();
        return $this->ok('country Deleted Successfully!');
    }
}
