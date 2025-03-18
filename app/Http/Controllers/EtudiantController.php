<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EtudiantController extends Controller
{
    public function create()
    {
        return view('etudiants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoUrl = null;
        if ($request->hasFile('photo')) {
            $photoUrl = $request->file('photo')->store('photos', 'public');
            $photoUrl = Storage::url($photoUrl);
        }

        $etudiantId = \DB::table('etudiant')->insertGetId([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'photo_url' => $photoUrl,
            'created_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Étudiant ajouté avec succès !');
    }
}