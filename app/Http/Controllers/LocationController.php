<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\User;

class LocationController extends Controller
{
    public function getCustomerLocations($customerId)
    {
        try {
            $customerId = decrypt($customerId);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {}

        try {

            $customer = User::findOrFail($customerId);
            $locations = $customer->locations()->with(['countryr', 'stater', 'cityr'])->get();
            
            return response()->json([
                'success' => true,
                'locations' => $locations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch locations.'
            ], 500);
        }
    }

    public function store(Request $request, $customerId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'dial_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'location_url' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $customer = User::findOrFail(decrypt($customerId));
            
            $data = $request->only([
                'name', 'contact_person', 'email', 'dial_code', 'phone_number',
                'address_line_1', 'address_line_2', 'country', 'state', 'city',
                'status', 'latitude', 'longitude', 'location_url'
            ]);
            $data['customer_id'] = $customer->id;

            $location = Location::create($data);
            $location->load(['countryr', 'stater', 'cityr']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Location added successfully.',
                'location' => $location
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add location.'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $location = Location::with(['countryr', 'stater', 'cityr'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'location' => $location
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'dial_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:500',
            'address_line_2' => 'nullable|string|max:500',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'location_url' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $location = Location::findOrFail($id);
            
            $data = $request->only([
                'name', 'contact_person', 'email', 'dial_code', 'phone_number',
                'address_line_1', 'address_line_2', 'country', 'state', 'city',
                'status', 'latitude', 'longitude', 'location_url'
            ]);

            $location->update($data);
            $location->load(['countryr', 'stater', 'cityr']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully.',
                'location' => $location
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete();

            return response()->json([
                'success' => true,
                'message' => 'Location deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete location.'
            ], 500);
        }
    }
}
