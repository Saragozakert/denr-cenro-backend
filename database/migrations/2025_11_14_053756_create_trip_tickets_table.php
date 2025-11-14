<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_trip_tickets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trip_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_request_id')->constrained()->onDelete('cascade');
            
            // Time Tracking
            $table->time('departure_time_office');
            $table->time('arrival_time_destination');
            $table->time('departure_time_destination');
            $table->time('arrival_time_office');
            
            // Distance and Fuel
            $table->decimal('distance_traveled', 8, 2);
            $table->string('distance_unit', 10);
            $table->decimal('gasoline_issued_purchased', 8, 2);
            $table->decimal('issued_from_stock', 8, 2);
            $table->decimal('gear_oil_used', 8, 2)->nullable();
            $table->decimal('lubricating_oil_used', 8, 2)->nullable();
            $table->decimal('grease_issued', 8, 2)->nullable();
            
            // Odometer Reading
            $table->decimal('odometer_start', 8, 2);
            $table->string('odometer_start_unit', 10);
            $table->decimal('odometer_end', 8, 2);
            $table->string('odometer_end_unit', 10);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_tickets');
    }
};