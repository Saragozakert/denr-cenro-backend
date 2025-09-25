<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
       
        DB::statement("ALTER TABLE units MODIFY COLUMN type ENUM('Vehicle', 'Motorcycle', 'Others') NOT NULL");
        
    }

    public function down()
    {
      
        DB::statement("ALTER TABLE units MODIFY COLUMN type ENUM('Vehicle', 'Motorcycle') NOT NULL");  
        DB::table('units')->where('type', 'Others')->update(['type' => 'Vehicle']);
    }
};