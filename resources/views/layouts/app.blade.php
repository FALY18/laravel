<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' 'unsafe-eval';">
    <title>Gestion des Présences</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: #e2e8f0;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }
        .navbar {
            background: #f8f9fa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 1rem 0;
        }
        .navbar .navbar-brand {
            font-weight: 700;
            color: #2c3e50 !important;
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }
        .navbar .navbar-brand:hover {
            color: #4a90e2 !important;
        }
        .navbar .nav-link {
            color: #2c3e50 !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: color 0.3s ease, border-bottom 0.3s ease;
        }
        .navbar .nav-link:hover {
            color: #4a90e2 !important;
            border-bottom: 2px solid #4a90e2;
        }
        .navbar .nav-link.active {
            color: #4a90e2 !important;
            font-weight: 600;
            border-bottom: 2px solid #4a90e2;
        }
        .container {
            margin-top: 30px;
            padding: 25px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        .container:hover {
            transform: translateY(-5px);
        }
        h1, h3 {
            color: #2c3e50;
            font-weight: 600;
        }
        .btn-primary {
            background: #4a90e2;
            border: none;
            transition: transform 0.2s ease, background 0.3s ease;
        }
        .btn-primary:hover {
            background: #357abd;
            transform: scale(1.05);
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            transition: transform 0.2s ease;
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: scale(1.05);
        }
        .btn-outline-danger {
            border-color: #e74c3c;
            color: #e74c3c;
        }
        .btn-outline-danger:hover {
            background: #e74c3c;
            color: #fff;
        }
        .table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .table th {
            background: #4a90e2;
            color: #fff;
            font-weight: 600;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-success {
            background: #28a745;
            border: none;
        }
        .btn-danger {
            background: #e74c3c;
            border: none;
        }
        .btn-success:hover, .btn-danger:hover {
            transform: scale(1.1);
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        /* Style personnalisé pour la barre de défilement (style Messenger) */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px; /* Largeur fine comme sur Messenger */
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; /* Fond clair */
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4a90e2; /* Couleur bleu discret */
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #357abd; /* Couleur plus foncée au survol */
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Gestion des Présences</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('etudiants/create') ? 'active' : '' }}" href="{{ route('etudiants.create') }}">Ajouter Étudiant</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('cours/create') ? 'active' : '' }}" href="{{ route('cours.create') }}">Ajouter Cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('presences/create') ? 'active' : '' }}" href="{{ route('presences.create') }}">Gérer Présences</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/chart.umd.min.js') }}"></script>
    @yield('scripts')
</body>
</html>