@extends('layouts.app')

@section('content')
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Taux d'assiduité</h5>
                    <p class="card-text">{{ $taux_assiduite }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Nombre d'étudiants</h5>
                    <p class="card-text">{{ $total_etudiants }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Présences / Absences</h5>
                    <p class="card-text">Présences : {{ $presences }} | Absences : {{ $absences }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section pour le graphique -->
    <div class="mt-5">
        <h2>Taux de présence par matière</h2>
        <p><em>Données officielles - Non modifiables</em></p>
        <div style="position: relative; width: 100%; max-width: 600px; height: 300px; margin: 0 auto;">
            <canvas id="tauxParCoursChart" data-taux-par-cours="{{ json_encode($taux_par_cours) }}"></canvas>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Script inline chargé.');

            // Vérifier si Chart.js est chargé
            if (typeof Chart === 'undefined') {
                console.error('Chart.js n\'est pas chargé !');
                return;
            } else {
                console.log('Chart.js est chargé:', Chart);
            }

            // Récupérer les données de taux par cours depuis l'attribut data
            const canvas = document.getElementById('tauxParCoursChart');
            if (!canvas) {
                console.error('Élément canvas #tauxParCoursChart introuvable !');
                return;
            }

            const tauxParCours = JSON.parse(canvas.getAttribute('data-taux-par-cours')) || [];
            console.log('Données récupérées:', tauxParCours);

            // Préparer les labels (noms des cours) et les données (taux)
            const labels = tauxParCours.map(item => item.nom);
            const data = tauxParCours.map(item => item.taux);
            console.log('Labels pour le graphique:', labels);
            console.log('Données pour le graphique:', data);

            // Créer le graphique avec Chart.js
            if (labels.length > 0 && data.length > 0) {
                console.log('Création du graphique...');
                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Taux de présence (%)',
                            data: data,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            barThickness: 20, // Réduire la largeur des barres
                        }]
                    },
                    options: {
                        maintainAspectRatio: false, // Permet au graphique de s'adapter au conteneur
                        responsive: true, // Active la responsivité
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Taux de présence (%)',
                                    font: {
                                        size: 12 // Réduire la taille de la police
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: 10 // Réduire la taille des ticks
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Matières',
                                    font: {
                                        size: 12
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    autoSkip: true, // Évite le chevauchement des labels
                                    maxRotation: 45, // Rotation des labels si trop de matières
                                    minRotation: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                enabled: false // Désactiver les tooltips pour éviter l'interaction
                            }
                        },
                        interaction: {
                            mode: 'none' // Désactiver toutes les interactions (clics, survols, etc.)
                        }
                    }
                });
                console.log('Graphique créé avec succès.');
            } else {
                console.warn('Aucune donnée disponible pour le graphique (labels ou data vide).');
            }
        });
    </script>
@endsection