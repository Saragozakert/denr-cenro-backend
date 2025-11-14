<?php
// app/Models/TripTicket.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_request_id',
        'departure_time_office',
        'arrival_time_destination',
        'departure_time_destination',
        'arrival_time_office',
        'distance_traveled',
        'distance_unit',
        'gasoline_issued_purchased',
        'issued_from_stock',
        'gear_oil_used',
        'lubricating_oil_used',
        'grease_issued',
        'odometer_start',
        'odometer_start_unit',
        'odometer_end',
        'odometer_end_unit'
    ];

    protected $casts = [
        'distance_traveled' => 'decimal:2',
        'gasoline_issued_purchased' => 'decimal:2',
        'issued_from_stock' => 'decimal:2',
        'gear_oil_used' => 'decimal:2',
        'lubricating_oil_used' => 'decimal:2',
        'grease_issued' => 'decimal:2',
        'odometer_start' => 'decimal:2',
        'odometer_end' => 'decimal:2',
    ];

    public function fuelRequest()
    {
        return $this->belongsTo(FuelRequest::class);
    }
}