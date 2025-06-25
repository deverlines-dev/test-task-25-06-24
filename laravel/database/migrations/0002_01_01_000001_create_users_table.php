<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->binary('rowid', 16)->nullable(false)->unique();

            $table->string('name');
            $table->string('password')->nullable(); // Если авторизация не через пароль, то null

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
