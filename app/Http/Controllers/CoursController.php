<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoursController extends Controller
{
    public function create()
    {
        return view('cours.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:100',
                'description' => 'nullable|string',
            ]);

            \DB::table('cours')->insert([
                'nom' => $request->nom,
                'description' => $request->description,
                'created_at' => now(),
            ]);

            return redirect()->route('dashboard')->with('success', 'Cours ajouté avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('cours.create')->with('error', 'Erreur lors de l\'ajout du cours : ' . $e->getMessage());
        }
    }
}