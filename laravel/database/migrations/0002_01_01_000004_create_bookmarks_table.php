<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Переименовал в user_bookmarks, т.принадлежит к пользователю
        Schema::create('user_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->binary('rowid', 16)->nullable(false)->unique();

            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('book_id')->constrained('books');

            $table->string('bookmark');

            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_bookmarks');
    }
};
