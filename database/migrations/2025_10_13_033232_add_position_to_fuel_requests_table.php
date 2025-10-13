<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->string('position')->nullable()->after('section');
        });
    }

    public function down()
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};