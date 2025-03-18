@extends('layouts.app')

@section('content')
    <h1>Ajouter un cours</h1>
    <form method="POST" action="{{ route('cours.store') }}">
        @csrf
        <div class="mb-3">
            <label for="nom" class="form-label">Nom du cours</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (optionnel)</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
@endsection