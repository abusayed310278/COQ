<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cleaning_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('cover_image');
            $table->string('title');
            $table->string('subtitle');
            $table->string('left_image');
            $table->text('what_we_offer_content');
            $table->json('what_we_offer_content_tags')->nullable();
            $table->text('why_choose_us_content');
            $table->json('why_choose_us_content_tags')->nullable();
            $table->string('right_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cleaning_services');
    }
};
