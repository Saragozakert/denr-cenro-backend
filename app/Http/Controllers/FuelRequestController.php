<?php
// app/Http/Controllers/FuelRequestController.php

namespace App\Http\Controllers;

use App\Models\FuelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Employee;
use App\Models\RequestingParty; // Add this import

class FuelRequestController extends Controller
{
    // Get all fuel requests (for admin)
    // Get all fuel requests (for admin)
    public function index(Request $request)
    {
        try {
            $fuelRequests = FuelRequest::orderBy('created_at', 'desc')->get();
            
            // For existing records without position data, try to populate them
            foreach ($fuelRequests as $fuelRequest) {
                // For requesting party position
                if (empty($fuelRequest->position) || $fuelRequest->position === 'N/A') {
                    $requestingParty = RequestingParty::where('full_name', $fuelRequest->requesting_party)->first();
                    if ($requestingParty) {
                        $fuelRequest->position = $requestingParty->position;
                        // Save the update to the database
                        $fuelRequest->save();
                    }
                }
                
                // FIXED: For approve section position - find by approved_by name
                if (empty($fuelRequest->approve_section_position) || $fuelRequest->approve_section_position === 'N/A') {
                    $employee = Employee::where('name', $fuelRequest->approved_by)->first();
                    if ($employee) {
                        $fuelRequest->approve_section_position = $employee->position;
                        // Save the update to the database
                        $fuelRequest->save();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'fuelRequests' => $fuelRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fuel requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'vehicle_type' => 'required|string|max:255',
                'model_name' => 'required|string|max:255',
                'plate_no' => 'required|string|max:255',
                'requesting_party' => 'required|string|max:255',
                'section' => 'required|string|max:255',
                'office' => 'required|string|max:255',
                'purchased_no' => 'nullable|string|max:255',
                'purpose' => 'required|string',
                'places_to_visit' => 'nullable|string',
                'authorized_passengers' => 'nullable|string',
                'fuel_type' => 'required|string|max:255',
                'gasoline_amount' => 'required|numeric|min:0',
                'withdrawn_by' => 'required|string|max:255',
                'approved_by' => 'required|string|max:255',
                'issued_by' => 'nullable|string|max:255',
                'approve_section_position' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the employee name from the ID if it's a numeric value
            $approvedByName = $request->approved_by;
            if (is_numeric($request->approved_by)) {
                $employee = Employee::find($request->approved_by);
                if ($employee) {
                    $approvedByName = $employee->name;
                }
            }

            // Get the requesting party name and position
            $requestingPartyName = $request->requesting_party;
            $position = null;
            
            if (is_numeric($request->requesting_party)) {
                $requestingParty = RequestingParty::find($request->requesting_party);
                if ($requestingParty) {
                    $requestingPartyName = $requestingParty->full_name;
                    $position = $requestingParty->position;
                }
            } else {
                // If requesting_party is already a name, find the position
                $requestingParty = RequestingParty::where('full_name', $request->requesting_party)->first();
                if ($requestingParty) {
                    $position = $requestingParty->position;
                }
            }

            // Get approve section position from employee by withdrawn_by name
            $approveSectionPosition = $request->approve_section_position;
            if (!$approveSectionPosition) {
                $employee = Employee::where('name', $request->withdrawn_by)->first();
                if ($employee) {
                    $approveSectionPosition = $employee->position;
                }
            }

            $fuelRequest = FuelRequest::create([
                'date' => now()->format('Y-m-d'),
                'vehicle_type' => $request->vehicle_type,
                'model_name' => $request->model_name,
                'plate_no' => $request->plate_no,
                'requesting_party' => $requestingPartyName,
                'section' => $request->section,
                'office' => $request->office,
                'purchased_no' => $request->purchased_no,
                'purpose' => $request->purpose,
                'places_to_visit' => $request->places_to_visit,
                'authorized_passengers' => $request->authorized_passengers,
                'fuel_type' => $request->fuel_type,
                'gasoline_amount' => $request->gasoline_amount,
                'withdrawn_by' => $request->withdrawn_by,
                'approved_by' => $approvedByName,
                'issued_by' => $request->issued_by,
                'position' => $position,
                'approve_section_position' => $approveSectionPosition,
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fuel request submitted successfully',
                'fuelRequest' => $fuelRequest
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit fuel request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

 
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:approved,rejected'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fuelRequest = FuelRequest::findOrFail($id);
            $fuelRequest->status = $request->status;
            $fuelRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Fuel request status updated successfully',
                'fuelRequest' => $fuelRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update fuel request status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get fuel requests for specific user (based on withdrawn_by)
    public function userRequests(Request $request)
    {
        try {
            $userName = $request->user()->full_name; // Assuming user has full_name attribute

            $fuelRequests = FuelRequest::where('withdrawn_by', $userName)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'fuelRequests' => $fuelRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user fuel requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateAmount(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'gasoline_amount' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $fuelRequest = FuelRequest::findOrFail($id);

            // Only allow editing of pending requests
            if ($fuelRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending requests can be edited'
                ], 400);
            }

            $fuelRequest->gasoline_amount = $request->gasoline_amount;
            $fuelRequest->save();

            return response()->json([
                'success' => true,
                'message' => 'Gasoline amount updated successfully',
                'fuelRequest' => $fuelRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update gasoline amount',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $fuelRequest = FuelRequest::findOrFail($id);

            // Optional: Add authorization check to ensure user can only delete their own requests
            // $userName = auth()->user()->full_name;
            // if ($fuelRequest->withdrawn_by !== $userName) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Unauthorized to delete this request'
            //     ], 403);
            // }

            $fuelRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fuel request deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete fuel request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}