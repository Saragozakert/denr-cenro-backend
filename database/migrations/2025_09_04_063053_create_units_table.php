<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Vehicle', 'Motorcycle']);
            $table->string('model');
            $table->string('plate_code')->unique();
            $table->string('assigned_to');
            $table->string('office');
            $table->enum('status', ['Active', 'Inactive', 'Maintenance'])->default('Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('units');
    }
};