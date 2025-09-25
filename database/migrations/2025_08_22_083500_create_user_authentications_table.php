<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_authentications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_authentications', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('age');
            $table->date('birthday');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('status', ['Active', 'Inactive', 'Suspended'])->default('Active');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_authentications');
    }
};