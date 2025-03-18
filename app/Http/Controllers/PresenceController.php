<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PresenceController extends Controller
{
    public function create()
    {
        // Récupérer tous les cours
        $cours = \DB::table('cours')->get();

        // Si une sélection est déjà validée (via session), la réutiliser
        $selectedCoursId = session()->get('selected_cours_id');
        $selectedDateTime = session()->get('selected_date_time');

        // Récupérer les étudiants et convertir en collection
        $etudiantsRaw = $selectedCoursId ? \DB::table('etudiant')->get() : [];
        $etudiants = collect($etudiantsRaw);

        // Récupérer le type de filtre (tous, présents, absents) depuis la session, par défaut "tous"
        $filterType = session()->get('filter_type', 'tous');

        // Filtrer les étudiants selon le type
        if ($selectedCoursId && $selectedDateTime) {
            $etudiants = $etudiants->filter(function ($etudiant) use ($selectedCoursId, $selectedDateTime, $filterType) {
                $presenceExists = \DB::table('presence')
                    ->where('etudiant_id', $etudiant->id)
                    ->where('cours_id', $selectedCoursId)
                    ->where('date', $selectedDateTime)
                    ->exists();
                $absenceExists = \DB::table('absence')
                    ->where('etudiant_id', $etudiant->id)
                    ->where('cours_id', $selectedCoursId)
                    ->where('date', $selectedDateTime)
                    ->exists();

                if ($filterType === 'presents' && !$presenceExists) {
                    return false;
                }
                if ($filterType === 'absents' && !$absenceExists) {
                    return false;
                }
                return true;
            });
        }

        // Calcul des présents, absents et total
        $presentCount = 0;
        $absentCount = 0;
        $totalStudents = count($etudiantsRaw);

        if ($selectedCoursId && $selectedDateTime) {
            $presentCount = \DB::table('presence')
                ->where('cours_id', $selectedCoursId)
                ->where('date', $selectedDateTime)
                ->count();

            $absentCount = \DB::table('absence')
                ->where('cours_id', $selectedCoursId)
                ->where('date', $selectedDateTime)
                ->count();
        }

        return view('presences.create', [
            'cours' => $cours,
            'selectedCoursId' => $selectedCoursId,
            'selectedDateTime' => $selectedDateTime,
            'etudiants' => $etudiants,
            'presentCount' => $presentCount,
            'absentCount' => $absentCount,
            'totalStudents' => $totalStudents,
            'filterType' => $filterType,
        ]);
    }

    public function selectCourse(Request $request)
    {
        $request->validate([
            'cours_id' => 'required|exists:cours,id',
            'date_time' => 'required|date',
        ]);

        // Stocker la sélection dans la session
        session(['selected_cours_id' => $request->input('cours_id')]);
        session(['selected_date_time' => $request->input('date_time')]);

        return redirect()->route('presences.create');
    }

    public function resetSelection()
    {
        // Vider la session pour la sélection et le filtre
        session()->forget(['selected_cours_id', 'selected_date_time', 'filter_type']);

        return redirect()->route('presences.create')->with('info', 'Sélection réinitialisée.');
    }

    public function applyFilter(Request $request)
    {
        $filterType = $request->input('filter_type', 'tous');
        session(['filter_type' => $filterType]);
        return redirect()->route('presences.create');
    }

    public function storePresence(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiant,id',
            'cours_id' => 'required|exists:cours,id',
            'date_time' => 'required|date',
            'status' => 'required|in:present,absent,cancel',
        ]);

        $etudiantId = $request->input('etudiant_id');
        $coursId = $request->input('cours_id');
        $dateTime = $request->input('date_time');
        $status = $request->input('status');

        if ($status === 'cancel') {
            // Annuler le statut en supprimant les entrées existantes
            \DB::table('presence')
                ->where('etudiant_id', $etudiantId)
                ->where('cours_id', $coursId)
                ->where('date', $dateTime)
                ->delete();
            \DB::table('absence')
                ->where('etudiant_id', $etudiantId)
                ->where('cours_id', $coursId)
                ->where('date', $dateTime)
                ->delete();
        } else {
            // Supprimer l'entrée existante avant d'ajouter une nouvelle
            \DB::table('presence')
                ->where('etudiant_id', $etudiantId)
                ->where('cours_id', $coursId)
                ->where('date', $dateTime)
                ->delete();
            \DB::table('absence')
                ->where('etudiant_id', $etudiantId)
                ->where('cours_id', $coursId)
                ->where('date', $dateTime)
                ->delete();

            if ($status === 'present') {
                \DB::table('presence')->insert([
                    'etudiant_id' => $etudiantId,
                    'cours_id' => $coursId,
                    'date' => $dateTime,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                \DB::table('absence')->insert([
                    'etudiant_id' => $etudiantId,
                    'cours_id' => $coursId,
                    'date' => $dateTime,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('presences.create')->with('success', 'Statut mis à jour avec succès.');
    }
}