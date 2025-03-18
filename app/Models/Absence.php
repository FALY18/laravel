<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $table = 'absence'; // Nom exact de la table créée dans pgAdmin
    protected $primaryKey = 'id';
    public $timestamps = true; // Si tes tables ont created_at et updated_at

    protected $fillable = ['etudiant_id', 'cours_id', 'date'];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'cours_id');
    }
}