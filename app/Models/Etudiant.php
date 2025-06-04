<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $table = 'etudiant'; 
    protected $primaryKey = 'id';
    public $timestamps = true; 

    protected $fillable = ['nom', 'prenom', 'photo_url'];

    public function presences()
    {
        return $this->hasMany(Presence::class, 'etudiant_id');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'etudiant_id');
    }
}