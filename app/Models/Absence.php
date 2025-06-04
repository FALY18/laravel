<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $table = 'absence'; 
    protected $primaryKey = 'id';
    public $timestamps = true; 

    protected $fillable = ['etudiant_id', 'cours_id', 'date', 'session_id'];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'etudiant_id');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'cours_id');
    }
}