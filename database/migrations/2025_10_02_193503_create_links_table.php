<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('url');                  // original URL
            $table->string('alias')->unique();      // short code/slug
            $table->unsignedBigInteger('click_count')->default(0);
            $table->timestamps();

            // Optional index you’ll likely query by:
            $table->index('alias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};

