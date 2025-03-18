@extends('layouts.app')

@section('content')
    <h1>Ajouter un étudiant</h1>
    <form method="POST" action="{{ route('etudiants.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" required>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo (optionnel)</label>
            <input type="file" class="form-control" id="photo" name="photo">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
@endsection