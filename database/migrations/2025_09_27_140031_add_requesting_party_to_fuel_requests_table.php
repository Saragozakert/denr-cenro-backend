<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('fuel_requests', 'requesting_party')) {
                $table->string('requesting_party')->after('plate_no');
            }
        });
    }



    public function down(): void
    {
        Schema::table('fuel_requests', function (Blueprint $table) {
            $table->dropColumn('requesting_party');
        });
    }
};