<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('etudiant', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->notNullable();
            $table->string('prenom', 100)->notNullable();
            $table->string('photo_url', 255)->nullable();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('etudiant');
    }
};