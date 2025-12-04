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
        Schema::table('job', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('approved_by_engineer_id')->nullable();
            $table->unsignedBigInteger('approved_by_billing_department_id')->nullable();
            $table->boolean('approved_by_engineer')->default(0);
            $table->boolean('approved_by_billing_department')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job', function (Blueprint $table) {
            $table->dropColumn([
                'location_id',
                'approved_by_engineer_id',
                'approved_by_billing_department_id',
                'approved_by_engineer',
                'approved_by_billing_department'
            ]);
        });
    }
};
