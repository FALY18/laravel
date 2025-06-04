<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistiques globl
        $totalEtudiants = \DB::table('etudiant')->count();
        $totalCours = \DB::table('cours')->count();
        $totalSessions = \DB::table('presence')->select('session_id')->distinct()->count('session_id');

        // Compter les présences et absences par étudiant unique  pour les 30 derniers jours
        $dateLimite = now()->subDays(30); 

        $presences = \DB::table('presence')
            ->select('etudiant_id')
            ->where('date', '>=', $dateLimite)
            ->distinct()
            ->count('etudiant_id');

        $absences = \DB::table('absence')
            ->select('etudiant_id')
            ->where('date', '>=', $dateLimite)
            ->distinct()
            ->count('etudiant_id');

        // Calcul du taux d'assiduité global 
        $totalPresencesAbsences = $presences + $absences;
        $tauxAssiduite = ($totalPresencesAbsences > 0 && $totalEtudiants > 0)
            ? ($presences / $totalPresencesAbsences) * 100
            : 0;

        $annee = request('annee', now()->year); 
        $dateDebut = Carbon::createFromDate($annee, 1, 1)->startOfDay();
        $dateFin = Carbon::createFromDate($annee, 12, 31)->endOfDay(); 

        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[] = sprintf('%d-%02d', $annee, $month); 
        }

        // Récupérer les présences et absences par cours et par mois
        $presenceStats = \DB::table('presence')
            ->join('cours', 'presence.cours_id', '=', 'cours.id')
            ->select(
                'cours.id as cours_id',
                'cours.nom as cours_nom',
                \DB::raw("TO_CHAR(presence.date, 'YYYY-MM') as month"),
                \DB::raw('COUNT(DISTINCT presence.etudiant_id) as total_presences')
            )
            ->whereBetween('presence.date', [$dateDebut, $dateFin])
            ->groupBy('cours.id', 'cours.nom', \DB::raw("TO_CHAR(presence.date, 'YYYY-MM')"))
            ->get();

        $absenceStats = \DB::table('absence')
            ->join('cours', 'absence.cours_id', '=', 'cours.id')
            ->select(
                'cours.id as cours_id',
                'cours.nom as cours_nom',
                \DB::raw("TO_CHAR(absence.date, 'YYYY-MM') as month"),
                \DB::raw('COUNT(DISTINCT absence.etudiant_id) as total_absences')
            )
            ->whereBetween('absence.date', [$dateDebut, $dateFin])
            ->groupBy('cours.id', 'cours.nom', \DB::raw("TO_CHAR(absence.date, 'YYYY-MM')"))
            ->get();

        // Récupérer tous les cours pour s'assurer qu'ils sont tous inclus
        $cours = \DB::table('cours')->select('id', 'nom')->get();

        // Construire les données pour le graphique
        $evolutionAssiduite = [];
        foreach ($cours as $c) {
            $dataPoints = [];
            foreach ($months as $month) {
                $presence = $presenceStats->where('cours_id', $c->id)
                    ->where('month', $month)
                    ->first();

                $absence = $absenceStats->where('cours_id', $c->id)
                    ->where('month', $month)
                    ->first();

                // Calculer le taux d'assiduité
                $totalPresences = $presence ? $presence->total_presences : 0;
                $totalAbsences = $absence ? $absence->total_absences : 0;
                $total = $totalPresences + $totalAbsences;
                $taux = $total > 0 ? ($totalPresences / $total) * 100 : 0; // 0 si aucune donnée

                $dataPoints[] = [
                    'month' => $month,
                    'taux' => round($taux, 2),
                ];
            }
            $evolutionAssiduite[] = [
                'nom' => $c->nom,
                'data' => $dataPoints,
            ];
        }

        Log::info('Statistiques globales', [
            'total_etudiants' => $totalEtudiants,
            'total_cours' => $totalCours,
            'total_sessions' => $totalSessions,
            'presences' => $presences,
            'absences' => $absences,
            'total_presences_absences' => $totalPresencesAbsences,
            'taux_assiduite' => $tauxAssiduite,
            'evolution_assiduite' => $evolutionAssiduite,
            'annee' => $annee,
        ]);

        return view('dashboard', compact('totalEtudiants', 'totalCours', 'totalSessions', 'presences', 'absences', 'tauxAssiduite', 'evolutionAssiduite', 'annee'));
    }
}