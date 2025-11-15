<?php
// database/migrations/2025_01_15_000000_create_trip_ticket_status_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripTicketStatusTable extends Migration
{
    public function up()
    {
        Schema::create('trip_ticket_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('trip_ticket_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('Submitted');
            $table->timestamp('date_submitted')->useCurrent();
            $table->timestamps();
            
            // Indexes
            $table->index('fuel_request_id');
            $table->index('trip_ticket_id');
            $table->index('status');
            $table->index('date_submitted');
        });
    }

    public function down()
    {
        Schema::dropIfExists('trip_ticket_status');
    }
}