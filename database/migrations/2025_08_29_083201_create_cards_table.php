<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('position')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('status', ['todo','in_progress', 'review', 'done']);
            $table->enum('priority', ['low', 'medium', 'high'])->default(null)->nullable();
            $table->decimal('estimated_hours', 5, 2)->nullable();
            $table->decimal('actual_hours', 5, 2)->default(0)->nullable();
            $table->timestamp('started_at')->nullable(); // When work actually begins (status changes to in_progress)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
