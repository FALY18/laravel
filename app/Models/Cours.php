<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    protected $table = 'cours'; // Nom exact de la table
    protected $primaryKey = 'id';
    public $timestamps = false; // Désactiver la gestion automatique des timestamps

    protected $fillable = ['nom', 'description', 'created_at'];
}