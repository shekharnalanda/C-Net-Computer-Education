<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('courses', function (Blueprint $table) { $table->id(); $table->string('code',20)->unique(); $table->string('title'); $table->string('title_hi')->nullable(); $table->string('duration',50); $table->string('level',50)->index(); $table->text('summary'); $table->string('eligibility')->nullable(); $table->json('modules')->nullable(); $table->json('careers')->nullable(); $table->boolean('is_active')->default(true)->index(); $table->unsignedInteger('sort_order')->default(0); $table->timestamps(); }); }
    public function down(): void { Schema::dropIfExists('courses'); }
};
