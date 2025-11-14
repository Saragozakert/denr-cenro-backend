<?php
// app/Http/Controllers/TripTicketController.php

namespace App\Http\Controllers;

use App\Models\TripTicket;
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
            $tripTicket = TripTicket::create($request->all());

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
}