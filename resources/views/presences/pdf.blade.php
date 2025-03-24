<!DOCTYPE html>
<html>
<head>
    <title>Rapport de Présence</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #d3d3d3;
            color: #333;
        }
        .status-present {
            color: green;
        }
        .status-absent {
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rapport de Présence</h2>
        <p><strong>Cours :</strong> {{ $cours->nom }}</p>
        <p><strong>Date :</strong> {{ $date }}</p>
        <p><strong>Filtre :</strong> {{ ucfirst($filterType) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($etudiants as $etudiant)
                @php
                    $presenceExists = \DB::table('presence')
                        ->where('etudiant_id', $etudiant->id)
                        ->where('cours_id', $cours->id)
                        ->where('date', $date)
                        ->where('session_id', $sessionId)
                        ->exists();
                    $absenceExists = \DB::table('absence')
                        ->where('etudiant_id', $etudiant->id)
                        ->where('cours_id', $cours->id)
                        ->where('date', $date)
                        ->where('session_id', $sessionId)
                        ->exists();
                    $status = $presenceExists ? 'Présent' : ($absenceExists ? 'Absent' : 'Non marqué');
                @endphp
                <tr>
                    <td>{{ $etudiant->id }}</td>
                    <td>
                        @if ($etudiant->photo_url)
                            <img src="{{ public_path($etudiant->photo_url) }}" alt="Photo de {{ $etudiant->nom }}" style="max-width: 50px; max-height: 50px; border-radius: 5px;" onerror="this.src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg=='">
                        @else
                            Aucune photo
                        @endif
                    </td>
                    <td>{{ $etudiant->nom }}</td>
                    <td>{{ $etudiant->prenom }}</td>
                    <td>
                        @if ($presenceExists)
                            <span class="status-present">Présent</span>
                        @elseif ($absenceExists)
                            <span class="status-absent">Absent</span>
                        @else
                            Non marqué
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>