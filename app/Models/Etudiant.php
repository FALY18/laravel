<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $table = 'etudiant'; // Nom exact de la table créée dans pgAdmin
    protected $primaryKey = 'id';
    public $timestamps = true; // Si tes tables ont created_at et updated_at

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