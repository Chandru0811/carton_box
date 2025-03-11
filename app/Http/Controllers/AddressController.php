<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Addresses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponses;

class AddressController extends Controller
{

    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        if ($user) {
            // Assuming the address is stored in a 'user_addresses' table and related to the user
            $addresses = Addresses::where('user_id', $user->id)->get();

            // Return the addresses as a JSON response
            return response()->json($addresses);
        }

        return response()->json([]);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'  => 'required|string|max:200',
            'email'       => 'required|email|max:200',
            'phone'       => 'required',
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
            'postalcode.required'    => 'Please provide a postal code.',
            'postalcode.digits'     => 'Postal code must be exactly 6 digits.',
            'address.required'       => 'Please provide an address.',
            'address.string'         => 'Address must be a valid text.',
            'type.required'          => 'Please provide the address type.',
            'type.string'            => 'Address type must be a valid text.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->has('default') && $request->default == 1) {
            Addresses::where('user_id', Auth::id())
                ->where('default', 1)
                ->update(['default' => 0]);
        }

        $addressData = $request->all();
        $addressData['user_id'] = Auth::id();

        $address = Addresses::create($addressData);

        return response()->json([
            'success' => true,
            'message' => 'Address Created Successfully!',
            'address' => $address
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $address = Addresses::find($id);

        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        return response()->json($address);
    }


    /**
     * Show the form for editing the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'  => 'required|string|max:200',
            'email'       => 'required|email|max:200',
            'phone'       => 'required',
            'postalcode'  => 'required|digits:6',
            'address'     => 'required|string',
            'type'        => 'required|string',
            'default'     => 'nullable|boolean'
        ], [
            'first_name.required'    => 'Please provide your first name.',
            'first_name.string'      => 'First name must be a valid text.',
            'first_name.max'         => 'First name may not exceed 200 characters.',
            'email.required'         => 'Please provide an email address.',
            'email.email'            => 'Please provide a valid email address.',
            'email.max'              => 'Email may not exceed 200 characters.',
            'phone.required'         => 'Please provide a phone number.',
            'postalcode.required'    => 'Please provide a postal code.',
            'postalcode.digits'      => 'Postal code must be exactly 6 digits.',
            'address.required'       => 'Please provide an address.',
            'address.string'         => 'Address must be a valid text.',
            'type.required'          => 'Please provide the address type.',
            'type.string'            => 'Address type must be a valid text.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $address = Addresses::where('id', $request->address_id)->where('user_id', Auth::id())->first();

        if (!$address) {
            return redirect()->back()->with('error', 'Address not found!');
        }

        $default = $request->has('default') ? $request->default : $request->default_hidden;

        if ($default == "1") {
            Addresses::where('user_id', Auth::id())
                ->where('default', 1)
                ->where('id', '!=', $request->address_id)
                ->update(['default' => 0]);
        }

        $address->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'postalcode' => $request->postalcode,
            'address'    => $request->address,
            'type'       => $request->type,
            'unit'       => $request->unit,
            'default'    => $default,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address Updated Successfully!',
            'address' => $address
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $address = Addresses::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Address not found or unauthorized action!'], 404);
        }

        $address->delete();

        return response()->json(['success' => true, 'message' => 'Address Deleted Successfully!'], 200);
    }
}
