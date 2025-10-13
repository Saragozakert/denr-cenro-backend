<?php
// app/Models/FuelRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'vehicle_type',
        'model_name',
        'plate_no',
        'requesting_party',
        'section',
        'office',
        'purchased_no',
        'purpose',
        'places_to_visit',
        'authorized_passengers',
        'fuel_type',
        'gasoline_amount',
        'withdrawn_by',
        'approved_by',
        'issued_by',
        'position', // Existing position for RequestingPartyTable
        'approve_section_position', // New position for ApproveSectionTable
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'gasoline_amount' => 'decimal:2'
    ];
}