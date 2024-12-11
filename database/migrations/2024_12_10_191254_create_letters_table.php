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


        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('code');
            $table->boolean('internal');
            $table->boolean('official');
            $table->boolean('has_attachments');
            $table->text('address')->nullable();
            $table->string('prefix');
            $table->string('sender');
            $table->string('receiver');
            $table->text('subject');
            $table->text('copyTo');
            $table->longText('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
