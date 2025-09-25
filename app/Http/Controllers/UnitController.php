<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index()
    {
        try {
            $units = Unit::all();
            return response()->json([
                'success' => true,
                'units' => $units
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch units',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                  'type' => 'required|in:Vehicle,Motorcycle,Others', // Add 'Others'
                'model' => 'required|string|max:255',
                'plateCode' => 'required|string|max:50|unique:units,plate_code',
                'assignedTo' => 'required|string|max:255',
                'office' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $unit = Unit::create([
                'type' => $request->type,
                'model' => $request->model,
                'plate_code' => $request->plateCode,
                'assigned_to' => $request->assignedTo,
                'office' => $request->office,
                'status' => 'Active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit created successfully',
                'unit' => $unit
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:Vehicle,Motorcycle,Others', // Add 'Others'
                'model' => 'required|string|max:255',
                'plate_code' => 'required|string|max:50|unique:units,plate_code,' . $id,
                'assigned_to' => 'required|string|max:255',
                'office' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $unit = Unit::findOrFail($id);
            $unit->update([
                'type' => $request->type,
                'model' => $request->model,
                'plate_code' => $request->plate_code,
                'assigned_to' => $request->assigned_to,
                'office' => $request->office
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Unit updated successfully',
                'unit' => $unit
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update unit',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}