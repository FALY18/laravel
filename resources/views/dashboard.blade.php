<!-- resources/views/dashboard.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tableau de Bord') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">
                    <h1 class="text-center mb-3 text-xl font-semibold">{{ __('Tableau de Bord') }}</h1>

                    <!-- Conteneur principal avec deux colonnes -->
                    <div class="flex flex-col lg:flex-row gap-4">
                        <!-- Colonne gauche : Graphique -->
                        <div class="lg:w-2/3 w-full">
                            <div class="card shadow-lg bg-gray-100"> <!-- Fond gris clair -->
                                <div class="card-body p-4">
                                    <h3 class="card-title text-lg font-semibold mb-2 text-gray-800">{{ __('Évolution de l\'Assiduité par Cours (par Mois)') }}</h3>
                                    <!-- Filtre pour sélectionner l'année -->
                                    <div class="mb-2">
                                        <label for="annee" class="form-label font-medium text-sm text-gray-700">{{ __('Année') }} :</label>
                                        <select id="annee" class="form-select w-full sm:w-32 border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm" onchange="window.location.href='?annee='+this.value">
                                            @for ($year = now()->year; $year >= now()->year - 5; $year--)
                                                <option value="{{ $year }}" {{ $annee == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    @if (empty($evolutionAssiduite) || count($evolutionAssiduite) === 0 || empty($evolutionAssiduite[0]['data']))
                                        <p class="text-muted text-center text-gray-500 text-sm">{{ __('Aucune donnée disponible pour le graphique. Ajoutez des cours et des présences pour voir les statistiques.') }}</p>
                                    @else
                                        <canvas id="evolutionAssiduiteChart" data-evolution-assiduite="{{ json_encode($evolutionAssiduite) }}" width="400" height="150"></canvas>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite : Statistiques générales -->
                        <div class="lg:w-1/3 w-full">
                            <div class="flex flex-col gap-4 bg-gray-100 p-4 rounded-lg shadow-lg"> <!-- Ajout de bg-gray-100, p-4 et rounded-lg -->
                                <div class="card text-white bg-primary shadow-lg rounded-lg">
                                    <div class="card-body p-3">
                                        <h5 class="card-title text-base font-semibold">{{ __('Total Étudiants') }}</h5>
                                        <p class="card-text text-3xl font-bold">{{ $totalEtudiants }}</p>
                                    </div>
                                </div>
                                <div class="card text-white bg-info shadow-lg rounded-lg">
                                    <div class="card-body p-3">
                                        <h5 class="card-title text-base font-semibold">{{ __('Total Cours') }}</h5>
                                        <p class="card-text text-3xl font-bold">{{ $totalCours }}</p>
                                    </div>
                                </div>
                                <div class="card text-white bg-secondary shadow-lg rounded-lg">
                                    <div class="card-body p-3">
                                        <h5 class="card-title text-base font-semibold">{{ __('Taux d\'Assiduité') }}</h5>
                                        <p class="card-text text-xl">{{ number_format($tauxAssiduite, 2) }}%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const canvas = document.getElementById('evolutionAssiduiteChart');
                if (!canvas) {
                    console.error('Canvas element not found');
                    return;
                }

                const evolutionData = JSON.parse(canvas.dataset.evolutionAssiduite);
                console.log('Données pour le graphique :', evolutionData);

                if (!evolutionData || evolutionData.length === 0 || !evolutionData[0].data || evolutionData[0].data.length === 0) {
                    console.error('Aucune donnée valide pour le graphique');
                    return;
                }

                // Générer les labels (mois) pour janvier à décembre
                const allMonths = evolutionData[0].data.map(point => point.month);
                allMonths.sort();

                const labels = allMonths.map(month => {
                    const d = new Date(month + '-01');
                    return d.toLocaleDateString('en-US', { month: 'short' });
                });

                const datasets = evolutionData.map((cours, index) => {
                    const colors = [
                        'rgba(54, 162, 235, 1)',  // Bleu
                        'rgba(255, 99, 132, 1)',  // Rouge
                        'rgba(75, 192, 192, 1)',  // Vert
                        'rgba(255, 206, 86, 1)',  // Jaune
                        'rgba(153, 102, 255, 1)', // Violet
                        'rgba(255, 159, 64, 1)',  // Orange
                    ];
                    const color = colors[index % colors.length];

                    const data = allMonths.map(month => {
                        const point = cours.data.find(p => p.month === month);
                        return point ? point.taux : 0;
                    });

                    return {
                        label: cours.nom,
                        data: data,
                        borderColor: color,
                        backgroundColor: color,
                        fill: false,
                        tension: 0.1,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                    };
                });

                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets,
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Taux d\'Assiduité (%)',
                                    font: { size: 12 }
                                },
                                ticks: {
                                    stepSize: 20,
                                    font: { size: 10 }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Mois',
                                    font: { size: 12 }
                                },
                                ticks: {
                                    maxTicksLimit: 12,
                                    font: { size: 10 }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: { size: 10 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(tooltipItems) {
                                        const month = allMonths[tooltipItems[0].dataIndex];
                                        const d = new Date(month + '-01');
                                        return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                                    },
                                    label: function(context) {
                                        const coursNom = context.dataset.label;
                                        const taux = context.parsed.y;
                                        return `${coursNom}: ${taux}%`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>