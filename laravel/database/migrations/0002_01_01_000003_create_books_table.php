<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->binary('row_id', 16)->nullable(false)->unique();

            $table->string('title');
            $table->string('description'); // Возможно нужно тип text
            $table->unsignedInteger('pages_count'); // unsigned, т.к не может быть -1 страница

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
