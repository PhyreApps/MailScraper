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
        Schema::create('scrapers', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->string('selector')->nullable();
            $table->longText('content')->nullable();
            $table->longText('settings')->nullable();
            $table->string('status')->nullable();

            $table->bigInteger('user_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapers');
    }
};
