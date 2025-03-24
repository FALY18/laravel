<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presence', function (Blueprint $table) {
            $table->string('session_id')->after('id'); // Ajoute session_id après la colonne id
        });

        Schema::table('absence', function (Blueprint $table) {
            $table->string('session_id')->after('id'); // Ajoute session_id après la colonne id
        });
    }

    public function down(): void
    {
        Schema::table('presence', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });

        Schema::table('absence', function (Blueprint $table) {
            $table->dropColumn('session_id');
        });
    }
};