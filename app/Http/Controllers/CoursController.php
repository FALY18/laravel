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
        $request->validate([
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $coursId = \DB::table('cours')->insertGetId([
            'nom' => $request->nom,
            'description' => $request->description,
            'created_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Cours ajouté avec succès !');
    }
}