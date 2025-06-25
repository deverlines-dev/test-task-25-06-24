<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_properties', function (Blueprint $table) {
            $table->id();
            $table->binary('row_id', 16)->nullable(false)->unique();

            $table->foreignId('user_id')->constrained('users');

            $table->string('property_key'); // переименовал key в property_key
            $table->string('property_value'); // в данном случае будет поддержка только string типов

            $table->unique(['user_id', 'property_key', 'property_value']);

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_properties');
    }
};
