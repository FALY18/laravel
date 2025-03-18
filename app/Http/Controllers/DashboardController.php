<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques globales
        $totalEtudiants = \DB::table('etudiant')->count();
        $presences = \DB::table('presence')->count();
        $absences = \DB::table('absence')->count();
        $tauxAssiduite = $totalEtudiants > 0 ? ($presences / ($presences + $absences)) * 100 : 0;

        // Débogage : Vérifier les statistiques globales
        Log::info('Statistiques globales', [
            'total_etudiants' => $totalEtudiants,
            'presences' => $presences,
            'absences' => $absences,
            'taux_assiduite' => $tauxAssiduite,
        ]);

        // Calculer les taux de présence par cours
        $cours = \DB::table('cours')->get();
        Log::info('Cours récupérés', ['cours' => $cours->toArray()]);

        $tauxParCours = [];
        foreach ($cours as $cour) {
            $presencesCours = \DB::table('presence')
                ->where('cours_id', $cour->id)
                ->count();
            $absencesCours = \DB::table('absence')
                ->where('cours_id', $cour->id)
                ->count();
            $total = $presencesCours + $absencesCours;
            $taux = $total > 0 ? ($presencesCours / $total) * 100 : 0;

            $tauxParCours[] = [
                'nom' => $cour->nom ?? 'Inconnu',
                'taux' => round($taux, 2),
            ];

            // Débogage : Vérifier les données pour chaque cours
            Log::info('Données pour le cours', [
                'cours_id' => $cour->id,
                'nom' => $cour->nom,
                'presences_cours' => $presencesCours,
                'absences_cours' => $absencesCours,
                'total' => $total,
                'taux' => $taux,
            ]);
        }

        // Débogage : Vérifier les données finales de $tauxParCours
        Log::info('Taux par cours calculé', ['taux_par_cours' => $tauxParCours]);

        return view('dashboard', [
            'taux_assiduite' => round($tauxAssiduite, 2),
            'total_etudiants' => $totalEtudiants,
            'presences' => $presences,
            'absences' => $absences,
            'taux_par_cours' => $tauxParCours,
        ]);
    }
}