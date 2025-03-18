@extends('layouts.app')

@section('content')
    <h1 class="text-center mb-4">Gérer les Présences/Absences</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <!-- Formulaire pour sélectionner le cours et la date/heure -->
    @if (!$selectedCoursId || !$selectedDateTime)
        <div class="card mb-4 shadow-lg">
            <div class="card-body">
                <h3 class="card-title mb-4 text-center">Sélectionner un Cours et une Date</h3>
                <form action="{{ route('presences.selectCourse') }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-5 mb-3">
                            <label for="cours_id" class="form-label">Cours</label>
                            <select name="cours_id" id="cours_id" class="form-select" required>
                                <option value="">Sélectionner un cours</option>
                                @foreach ($cours as $cour)
                                    <option value="{{ $cour->id }}">
                                        {{ $cour->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="date_time" class="form-label">Date et Heure</label>
                            <input type="datetime-local" name="date_time" id="date_time" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <button type="submit" class="btn btn-primary w-100">Valider</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Liste des étudiants après validation -->
        <div class="card shadow-lg">
            <div class="card-body">
                <h3 class="card-title mb-4">
                    Cours : {{ $cours->where('id', $selectedCoursId)->first()->nom ?? 'Inconnu' }} - Date : {{ \Carbon\Carbon::parse($selectedDateTime)->format('d/m/Y H:i') }}
                </h3>
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <a href="{{ route('presences.resetSelection') }}" class="btn btn-secondary me-2">Changer la sélection</a>
                    <form action="{{ route('presences.applyFilter') }}" method="POST" class="d-inline">
                        @csrf
                        <div class="input-group">
                            <select name="filter_type" class="form-select" onchange="this.form.submit()">
                                <option value="tous" {{ $filterType === 'tous' ? 'selected' : '' }}>Tous</option>
                                <option value="presents" {{ $filterType === 'presents' ? 'selected' : '' }}>Présents</option>
                                <option value="absents" {{ $filterType === 'absents' ? 'selected' : '' }}>Absents</option>
                            </select>
                        </div>
                    </form>
                    <div class="filter-stats d-flex align-items-center ms-3">
                        <span class="me-3">Présents : {{ $presentCount }}</span>
                        <span class="me-3">Absents : {{ $absentCount }}</span>
                        <span>Total : {{ $totalStudents }}</span>
                    </div>
                </div>
                @if ($etudiants->isEmpty())
                    <p class="text-muted">Aucun étudiant trouvé.</p>
                @else
                    <div class="table-responsive custom-scrollbar" style="max-height: 350px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Actions</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($etudiants as $etudiant)
                                    @php
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
                                    @endphp
                                    <tr>
                                        <td>{{ $etudiant->nom }}</td>
                                        <td>{{ $etudiant->prenom }}</td>
                                        <td>
                                            <form action="{{ route('presences.storePresence') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                                <input type="hidden" name="cours_id" value="{{ $selectedCoursId }}">
                                                <input type="hidden" name="date_time" value="{{ $selectedDateTime }}">
                                                <input type="hidden" name="status" value="present">
                                                <button type="submit" class="btn btn-success btn-sm me-2" {{ $presenceExists || $absenceExists ? 'disabled' : '' }} onclick="return confirm('Marquer cet étudiant comme présent ?')">Présent</button>
                                            </form>
                                            <form action="{{ route('presences.storePresence') }}" method="POST" style="display: inline;">
                                                @csrf
                                                <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                                <input type="hidden" name="cours_id" value="{{ $selectedCoursId }}">
                                                <input type="hidden" name="date_time" value="{{ $selectedDateTime }}">
                                                <input type="hidden" name="status" value="absent">
                                                <button type="submit" class="btn btn-danger btn-sm" {{ $presenceExists || $absenceExists ? 'disabled' : '' }} onclick="return confirm('Marquer cet étudiant comme absent ?')">Absent</button>
                                            </form>
                                            @if ($presenceExists || $absenceExists)
                                                <form action="{{ route('presences.storePresence') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                                    <input type="hidden" name="cours_id" value="{{ $selectedCoursId }}">
                                                    <input type="hidden" name="date_time" value="{{ $selectedDateTime }}">
                                                    <input type="hidden" name="status" value="cancel">
                                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Annuler le statut de cet étudiant ?')">Annuler</button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($presenceExists)
                                                <span class="badge bg-success">Présent</span>
                                            @elseif ($absenceExists)
                                                <span class="badge bg-danger">Absent</span>
                                            @else
                                                <span class="badge bg-secondary">Non marqué</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection