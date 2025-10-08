<?php
// database/migrations/[timestamp]_add_places_to_visit_and_authorized_passengers_to_fuel_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlacesToVisitAndAuthorizedPassengersToFuelRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->text('places_to_visit')->nullable()->after('purpose');
            $table->text('authorized_passengers')->nullable()->after('places_to_visit');
        });
    }

    public function down()
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->dropColumn(['places_to_visit', 'authorized_passengers']);
        });
    }
}