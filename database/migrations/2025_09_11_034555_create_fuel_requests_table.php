<?php
// database/migrations/2024_01_01_000000_create_fuel_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('fuel_requests', function (Blueprint $table) {
            $table->id();
            $table->date('date')->default(DB::raw('CURRENT_DATE'));
            $table->string('vehicle_type');
            $table->string('model_name');
            $table->string('plate_no');
            $table->string('requesting_party');
            $table->string('section');
            $table->string('office');
            $table->string('purchased_no')->nullable();
            $table->text('purpose');
            $table->string('fuel_type');
            $table->decimal('gasoline_amount', 8, 2);
            $table->string('withdrawn_by');
            $table->string('approved_by');
            $table->string('issued_by')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuel_requests');
    }
}