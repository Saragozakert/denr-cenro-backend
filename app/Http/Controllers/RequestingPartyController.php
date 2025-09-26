<?php

namespace App\Http\Controllers;

use App\Models\RequestingParty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestingPartyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $requestingParties = RequestingParty::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'requestingParties' => $requestingParties,
                'message' => 'Requesting parties retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve requesting parties',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'position' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $requestingParty = RequestingParty::create([
                'full_name' => $request->name,
                'division_section' => $request->division,
                'position' => $request->position,
            ]);

            return response()->json([
                'success' => true,
                'requestingParty' => $requestingParty,
                'message' => 'Requesting party created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create requesting party',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $requestingParty = RequestingParty::find($id);

            if (!$requestingParty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requesting party not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'division' => 'required|string|max:255',
                'position' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $requestingParty->update([
                'full_name' => $request->name,
                'division_section' => $request->division,
                'position' => $request->position,
            ]);

            return response()->json([
                'success' => true,
                'requestingParty' => $requestingParty,
                'message' => 'Requesting party updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update requesting party',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $requestingParty = RequestingParty::find($id);

            if (!$requestingParty) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requesting party not found'
                ], 404);
            }

            $requestingParty->delete();

            return response()->json([
                'success' => true,
                'message' => 'Requesting party deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete requesting party',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}