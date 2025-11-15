<?php
// app/Models/TripTicketStatus.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripTicketStatus extends Model
{
    use HasFactory;

    protected $table = 'trip_ticket_status';

    protected $fillable = [
        'fuel_request_id',
        'trip_ticket_id',
        'status',
        'date_submitted'
    ];

    protected $casts = [
        'date_submitted' => 'datetime'
    ];

    public function fuelRequest()
    {
        return $this->belongsTo(FuelRequest::class);
    }

    public function tripTicket()
    {
        return $this->belongsTo(TripTicket::class);
    }
}