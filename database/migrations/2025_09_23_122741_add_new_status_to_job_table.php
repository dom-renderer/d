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
            $table->enum( 'status', [ 'PENDING', 'INPROGRESS', 'ONHOLD', 'COMPLETED', 'CANCELLED' ] )->default('PENDING')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job', function (Blueprint $table) {
            $table->enum( 'status', [ 'PENDING', 'INPROGRESS', 'COMPLETED', 'CANCELLED' ] )->default('PENDING')->change();
        });
    }
};
