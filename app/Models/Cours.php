<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    protected $table = 'cours'; 
    protected $primaryKey = 'id';
    public $timestamps = false; 

    protected $fillable = ['nom', 'description', 'created_at'];
}