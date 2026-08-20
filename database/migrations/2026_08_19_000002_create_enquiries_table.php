<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('enquiries', function (Blueprint $table) { $table->id(); $table->string('name',80); $table->string('phone',20)->index(); $table->string('email')->nullable(); $table->string('city',80)->nullable(); $table->string('course_code',40)->index(); $table->text('message')->nullable(); $table->enum('status',['new','contacted','closed'])->default('new')->index(); $table->string('ip_address',45)->nullable(); $table->timestamps(); }); }
    public function down(): void { Schema::dropIfExists('enquiries'); }
};
