<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Addresses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponses;

class AddressController extends Controller
{
    use ApiResponses;


    public function index()
    {
        $userId = Auth::id();

        if (!$userId) {
            return $this->error('Unauthorized access. User authentication is required.', null, 401);
        }

        $addresses = Addresses::where('user_id', $userId)->get();

        if ($addresses->isEmpty()) {
            return $this->error('No addresses found for the authenticated user.', null, 404);
        }

        return $this->success('Addresses retrieved successfully!', $addresses);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'  => 'required|string|max:200',
            'email'       => 'required|email|max:200',
            'phone'       => 'required|digits:10',
            'postalcode'  => 'required|digits:6',
            'address'     => 'required|string',
            'type'        => 'required|string',
            'default'     => 'nullable|in:0,1,true,false',
        ], [
            'first_name.required'    => 'Please provide your first name.',
            'first_name.string'      => 'First name must be a valid text.',
            'first_name.max'         => 'First name may not exceed 200 characters.',
            'email.required'         => 'Please provide an email address.',
            'email.email'            => 'Please provide a valid email address.',
            'email.max'              => 'Email may not exceed 200 characters.',
            'phone.required'         => 'Please provide a phone number.',
            'phone.digits'           => 'Phone number must be exactly 10 digits.',
            'postalcode.required'    => 'Please provide a postal code.',
            'postalcode.digits'      => 'Postal code must be exactly 6 digits.',
            'address.required'       => 'Please provide an address.',
            'address.string'         => 'Address must be a valid text.',
            'type.required'          => 'Please provide the address type.',
            'type.string'            => 'Address type must be a valid text.',
            'default.in'             => 'The default field must be true or false.',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        if ($request->has('default')) {
            $request->merge(['default' => filter_var($request->default, FILTER_VALIDATE_BOOLEAN)]);
        }

        if ($request->default) {
            Addresses::where('user_id', Auth::id())
                ->where('default', 1)
                ->update(['default' => 0]);
        }

        $addressData = $request->all();
        $addressData['user_id'] = Auth::id();

        $address = Addresses::create($addressData);

        return $this->success('Address created successfully!', $address);
    }


    public function show(string $id)
    {
        $address = Addresses::find($id);

        if (!$address) {
            return $this->error('Address not found!', 404);
        }

        return $this->success('Address Retrieved Successfully!', $address);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name'  => 'required|string|max:200',
            'email'       => 'required|email|max:200',
            'phone'       => 'required|digits:10',
            'postalcode'  => 'required|digits:6',
            'address'     => 'required|string',
            'type'        => 'required|string',
            'default'     => 'nullable|boolean',
        ], [
            'first_name.required'    => 'Please provide your first name.',
            'first_name.string'      => 'First name must be a valid text.',
            'first_name.max'         => 'First name may not exceed 200 characters.',
            'email.required'         => 'Please provide an email address.',
            'email.email'            => 'Please provide a valid email address.',
            'email.max'              => 'Email may not exceed 200 characters.',
            'phone.required'         => 'Please provide a phone number.',
            'phone.digits'           => 'Phone number must be exactly 10 digits.',
            'postalcode.required'    => 'Please provide a postal code.',
            'postalcode.digits'      => 'Postal code must be exactly 6 digits.',
            'address.required'       => 'Please provide an address.',
            'address.string'         => 'Address must be a valid text.',
            'type.required'          => 'Please provide the address type.',
            'type.string'            => 'Address type must be a valid text.',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first(), 422);
        }

        $address = Addresses::where('id', $id)->where('user_id', Auth::guard('api')->id())->first();

        if (!$address) {
            return $this->error('Address not found!', 404);
        }

        $default = $request->has('default') ? $request->default : $request->default_hidden;
        if ($default == "1") {
            Addresses::where('user_id', Auth::guard('api')->id())
                ->where('default', 1)
                ->where('id', '!=', $id)
                ->update(['default' => 0]);
        }

        $address->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'postalcode' => $request->postalcode,
            'address'    => $request->address,
            'type'       => $request->type,
            'unit'       => $request->unit,
            'default'    => $default,
        ]);

        return $this->success('Address updated successfully!', $address);
    }

    public function destroy(string $id)
    {
        $address = Addresses::find($id);

        if (!$address) {
            return $this->error('Address not found!', 404);
        }

        $address->delete();

        return $this->ok('Address deleted successfully!');
    }

    public function changeSelectedId(Request $request)
    {
        $selectedId = $request->input('selected_id');

        if (!$selectedId) {
            return response()->json(['success' => false, 'message' => 'No address selected.'], 400);
        }

        session(['selectedId' => $selectedId]);

        $selectedAddress = Addresses::find($selectedId);

        return response()->json(['success' => true, 'selectedId' => $selectedId, 'selectedAddress' => $selectedAddress]);
    }
}
