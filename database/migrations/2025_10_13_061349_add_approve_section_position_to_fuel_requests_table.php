<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_approve_section_position_to_fuel_requests_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApproveSectionPositionToFuelRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->string('approve_section_position')->nullable()->after('position');
        });
    }

    public function down()
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->dropColumn('approve_section_position');
        });
    }
}