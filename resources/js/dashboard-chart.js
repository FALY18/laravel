document.addEventListener('DOMContentLoaded', function () {
    console.log('Script dashboard-chart.js chargé.');

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
    console.log('Données récupérées dans dashboard-chart.js:', tauxParCours);

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
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Taux de présence (%)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Matières'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
        console.log('Graphique créé avec succès.');
    } else {
        console.warn('Aucune donnée disponible pour le graphique (labels ou data vide).');
    }
});