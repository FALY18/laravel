<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PresenceController extends Controller
{
    public function create()
    {
        try {
            // Récupérer tous les cours
            $cours = \DB::table('cours')->get();

            // Si une sélection est déjà validée (via session), la réutiliser
            $selectedCoursId = session()->get('selected_cours_id');
            $selectedDateTime = session()->get('selected_date_time');
            $sessionId = session()->get('session_id');

            // Récupérer les étudiants et convertir en collection
            $etudiantsRaw = $selectedCoursId ? \DB::table('etudiant')->get() : [];
            $etudiants = collect($etudiantsRaw);

            // Récupérer le type de filtre (tous, présents, absents) depuis la session, par défaut "tous"
            $filterType = session()->get('filter_type', 'tous');

            // Filtrer les étudiants selon le type pour la session actuelle
            if ($selectedCoursId && $selectedDateTime && $sessionId) {
                $etudiants = $etudiants->filter(function ($etudiant) use ($selectedCoursId, $selectedDateTime, $filterType, $sessionId) {
                    $presenceExists = \DB::table('presence')
                        ->where('etudiant_id', $etudiant->id)
                        ->where('cours_id', $selectedCoursId)
                        ->where('date', $selectedDateTime)
                        ->where('session_id', $sessionId)
                        ->exists();
                    $absenceExists = \DB::table('absence')
                        ->where('etudiant_id', $etudiant->id)
                        ->where('cours_id', $selectedCoursId)
                        ->where('date', $selectedDateTime)
                        ->where('session_id', $sessionId)
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

            // Calcul des présents, absents et total pour la session actuelle
            $presentCount = 0;
            $absentCount = 0;
            $totalStudents = count($etudiantsRaw);

            if ($selectedCoursId && $selectedDateTime && $sessionId) {
                $presentCount = \DB::table('presence')
                    ->where('cours_id', $selectedCoursId)
                    ->where('date', $selectedDateTime)
                    ->where('session_id', $sessionId)
                    ->count();

                $absentCount = \DB::table('absence')
                    ->where('cours_id', $selectedCoursId)
                    ->where('date', $selectedDateTime)
                    ->where('session_id', $sessionId)
                    ->count();
            }

            return view('presences.create', [
                'cours' => $cours,
                'selectedCoursId' => $selectedCoursId,
                'selectedDateTime' => $selectedDateTime,
                'sessionId' => $sessionId,
                'etudiants' => $etudiants,
                'presentCount' => $presentCount,
                'absentCount' => $absentCount,
                'totalStudents' => $totalStudents,
                'filterType' => $filterType,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Erreur lors du chargement de la page de gestion des présences : ' . $e->getMessage());
        }
    }

    public function selectCourse(Request $request)
    {
        try {
            $request->validate([
                'cours_id' => 'required|exists:cours,id',
                'date_time' => 'required|date',
            ]);

            $coursId = $request->input('cours_id');
            $dateTime = $request->input('date_time');

            // Générer un nouvel identifiant unique pour cette session
            $sessionId = Str::uuid()->toString();

            // Stocker la sélection dans la session
            session([
                'selected_cours_id' => $coursId,
                'selected_date_time' => $dateTime,
                'session_id' => $sessionId,
            ]);

            return redirect()->route('presences.create')->with('info', 'Nouvelle session de présence démarrée.');
        } catch (\Exception $e) {
            return redirect()->route('presences.create')->with('error', 'Erreur lors de la sélection du cours : ' . $e->getMessage());
        }
    }

    public function resetSelection()
    {
        try {
            // Vider la session pour la sélection, le filtre et l'ID de session
            session()->forget(['selected_cours_id', 'selected_date_time', 'session_id', 'filter_type']);

            return redirect()->route('presences.create')->with('info', 'Sélection réinitialisée.');
        } catch (\Exception $e) {
            return redirect()->route('presences.create')->with('error', 'Erreur lors de la réinitialisation de la sélection : ' . $e->getMessage());
        }
    }

    public function applyFilter(Request $request)
    {
        try {
            $filterType = $request->input('filter_type', 'tous');
            session(['filter_type' => $filterType]);
            return redirect()->route('presences.create');
        } catch (\Exception $e) {
            return redirect()->route('presences.create')->with('error', 'Erreur lors de l\'application du filtre : ' . $e->getMessage());
        }
    }

    public function storePresence(Request $request)
    {
        try {
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
            $sessionId = session()->get('session_id');

            if (!$sessionId) {
                return redirect()->route('presences.create')->with('error', 'Session invalide. Veuillez sélectionner un cours et une date.');
            }

            if ($status === 'cancel') {
                // Supprimer uniquement les entrées de la session actuelle
                \DB::table('presence')
                    ->where('etudiant_id', $etudiantId)
                    ->where('cours_id', $coursId)
                    ->where('date', $dateTime)
                    ->where('session_id', $sessionId)
                    ->delete();
                \DB::table('absence')
                    ->where('etudiant_id', $etudiantId)
                    ->where('cours_id', $coursId)
                    ->where('date', $dateTime)
                    ->where('session_id', $sessionId)
                    ->delete();
            } else {
                // Supprimer uniquement les entrées de la session actuelle avant d'ajouter une nouvelle
                \DB::table('presence')
                    ->where('etudiant_id', $etudiantId)
                    ->where('cours_id', $coursId)
                    ->where('date', $dateTime)
                    ->where('session_id', $sessionId)
                    ->delete();
                \DB::table('absence')
                    ->where('etudiant_id', $etudiantId)
                    ->where('cours_id', $coursId)
                    ->where('date', $dateTime)
                    ->where('session_id', $sessionId)
                    ->delete();

                // Ajouter une nouvelle entrée avec le session_id
                if ($status === 'present') {
                    \DB::table('presence')->insert([
                        'session_id' => $sessionId,
                        'etudiant_id' => $etudiantId,
                        'cours_id' => $coursId,
                        'date' => $dateTime,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    \DB::table('absence')->insert([
                        'session_id' => $sessionId,
                        'etudiant_id' => $etudiantId,
                        'cours_id' => $coursId,
                        'date' => $dateTime,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return redirect()->route('presences.create')->with('success', 'Statut mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('presences.create')->with('error', 'Erreur lors de la mise à jour du statut : ' . $e->getMessage());
        }
    }
}