<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presence', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->default(null);
        });

        Schema::table('absence', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    public function down(): void
    {
        Schema::table('presence', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        Schema::table('absence', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};