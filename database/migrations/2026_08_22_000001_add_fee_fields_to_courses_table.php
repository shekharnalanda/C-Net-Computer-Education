<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table): void {
            $table->decimal('fee_amount', 10, 2)->nullable()->after('duration');
            $table->string('fee_note', 160)->nullable()->after('fee_amount');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table): void {
            $table->dropColumn(['fee_amount', 'fee_note']);
        });
    }
};
