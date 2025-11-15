<?php
// app/Http/Controllers/TripTicketController.php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\TripTicketStatus;
use App\Models\FuelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripTicketController extends Controller
{
   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fuel_request_id' => 'required|exists:fuel_requests,id',
            'departure_time_office' => 'required|date_format:H:i',
            'arrival_time_destination' => 'required|date_format:H:i',
            'departure_time_destination' => 'required|date_format:H:i',
            'arrival_time_office' => 'required|date_format:H:i',
            'distance_traveled' => 'required|numeric|min:0',
            'distance_unit' => 'required|in:KM,M',
            'gasoline_issued_purchased' => 'required|numeric|min:0',
            'issued_from_stock' => 'required|numeric|min:0',
            'gear_oil_used' => 'nullable|numeric|min:0',
            'lubricating_oil_used' => 'nullable|numeric|min:0',
            'grease_issued' => 'nullable|numeric|min:0',
            'odometer_start' => 'required|numeric|min:0',
            'odometer_start_unit' => 'required|in:KM,M',
            'odometer_end' => 'required|numeric|min:0',
            'odometer_end_unit' => 'required|in:KM,M',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create the trip ticket
            $tripTicket = TripTicket::create($request->all());

            // UPDATE: Find and update the existing status record
            $tripTicketStatus = TripTicketStatus::where('fuel_request_id', $request->fuel_request_id)->first();
            
            if ($tripTicketStatus) {
                // Update existing status record
                $tripTicketStatus->update([
                    'trip_ticket_id' => $tripTicket->id,
                    'status' => 'Submitted',
                    'date_submitted' => now()
                ]);
            } else {
                // Create new status record if not exists (fallback)
                TripTicketStatus::create([
                    'fuel_request_id' => $request->fuel_request_id,
                    'trip_ticket_id' => $tripTicket->id,
                    'status' => 'Submitted',
                    'date_submitted' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Trip ticket submitted successfully!',
                'trip_ticket' => $tripTicket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit trip ticket.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByFuelRequest($fuelRequestId)
    {
        try {
            $tripTicket = TripTicket::where('fuel_request_id', $fuelRequestId)->first();

            if (!$tripTicket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip ticket not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'trip_ticket' => $tripTicket
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trip ticket.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAll()
    {
        try {
            $tripTickets = TripTicket::with('fuelRequest')->get();

            return response()->json([
                'success' => true,
                'trip_tickets' => $tripTickets
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trip tickets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // NEW METHOD: Get trip tickets for user table display
    public function getUserTripTickets(Request $request)
    {
        try {
            $userName = $request->user()->full_name;

            $tripTickets = TripTicketStatus::with(['fuelRequest'])
                ->whereHas('fuelRequest', function ($query) use ($userName) {
                    $query->where('withdrawn_by', $userName);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($status) {
                    return [
                        'id' => $status->id,
                        'no' => $status->id, // Sequence number
                        'date_requested' => $status->fuelRequest->date, // From fuel request
                        'fuel_amount' => $status->fuelRequest->gasoline_amount, // From fuel request
                        'fuel_type' => $status->fuelRequest->fuel_type, // From fuel request
                        'status' => $status->status, // From trip_ticket_status table
                        'date_submitted' => $status->date_submitted, // From trip_ticket_status table
                        'created_at' => $status->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'tripTickets' => $tripTickets
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trip tickets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // NEW METHOD: Get all trip ticket statuses (for admin if needed)
    public function getAllStatuses()
    {
        try {
            $tripTicketStatuses = TripTicketStatus::with(['fuelRequest', 'tripTicket'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'trip_ticket_statuses' => $tripTicketStatuses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trip ticket statuses.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkTripTicketExists($fuelRequestId)
    {
        try {
            $tripTicketExists = TripTicket::where('fuel_request_id', $fuelRequestId)->exists();

            return response()->json([
                'success' => true,
                'trip_ticket_exists' => $tripTicketExists
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check trip ticket existence.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Add this method to get fuel requests with trip ticket status
    public function getFuelRequestsWithTripTicketStatus(Request $request)
    {
        try {
            $userName = $request->user()->full_name;

            $fuelRequests = FuelRequest::where('withdrawn_by', $userName)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($fuelRequest) {
                    $hasTripTicket = TripTicket::where('fuel_request_id', $fuelRequest->id)->exists();

                    return [
                        'id' => $fuelRequest->id,
                        'date' => $fuelRequest->date,
                        'vehicle_type' => $fuelRequest->vehicle_type,
                        'model_name' => $fuelRequest->model_name,
                        'plate_no' => $fuelRequest->plate_no,
                        'requesting_party' => $fuelRequest->requesting_party,
                        'section' => $fuelRequest->section,
                        'office' => $fuelRequest->office,
                        'purchased_no' => $fuelRequest->purchased_no,
                        'purpose' => $fuelRequest->purpose,
                        'places_to_visit' => $fuelRequest->places_to_visit,
                        'authorized_passengers' => $fuelRequest->authorized_passengers,
                        'fuel_type' => $fuelRequest->fuel_type,
                        'gasoline_amount' => $fuelRequest->gasoline_amount,
                        'withdrawn_by' => $fuelRequest->withdrawn_by,
                        'approved_by' => $fuelRequest->approved_by,
                        'issued_by' => $fuelRequest->issued_by,
                        'status' => $fuelRequest->status,
                        'has_trip_ticket' => $hasTripTicket, // This indicates if trip ticket exists
                        'created_at' => $fuelRequest->created_at,
                        'updated_at' => $fuelRequest->updated_at
                    ];
                });

            return response()->json([
                'success' => true,
                'fuelRequests' => $fuelRequests
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fuel requests.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAdminTripTickets(Request $request)
    {
        try {
            $tripTickets = TripTicketStatus::with(['fuelRequest'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($status) {
                    return [
                        'id' => $status->id,
                        'no' => $status->id,
                        'date_requested' => $status->fuelRequest->date,
                        'withdrawn_by' => $status->fuelRequest->withdrawn_by,
                        'section' => $status->fuelRequest->section,
                        'status' => $status->status,
                        'date_submitted' => $status->date_submitted ? $status->date_submitted->format('Y-m-d H:i:s') : 'Pending',
                        'created_at' => $status->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'tripTickets' => $tripTickets
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin trip tickets.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // NEW METHOD: Update trip ticket status (for admin)
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Pending,Approved,Rejected,Completed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tripTicketStatus = TripTicketStatus::findOrFail($id);
            $tripTicketStatus->status = $request->status;
            $tripTicketStatus->save();

            return response()->json([
                'success' => true,
                'message' => 'Trip ticket status updated successfully!',
                'trip_ticket_status' => $tripTicketStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update trip ticket status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}