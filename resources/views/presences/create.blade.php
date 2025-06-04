<x-app-layout>
   

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-center mb-6 text-2xl font-semibold text-gray-800">{{ __('Gérer les Présences/Absences') }}</h1>

                    @if (!$selectedCoursId || !$selectedDateTime || !$sessionId)
                        <!-- Formulaire de sélection du cours et de la date -->
                        <div class="card mb-4 shadow-lg border border-gray-200 rounded-lg">
                            <div class="card-body p-6">
                                <h3 class="card-title mb-4 text-center text-xl font-semibold text-gray-700">{{ __('Sélectionner un Cours et une Date') }}</h3>
                                <form action="{{ route('presences.selectCourse') }}" method="POST">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                        <div>
                                            <label for="cours_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Cours') }}</label>
                                            <select name="cours_id" id="cours_id" class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out" required>
                                                <option value="">{{ __('Sélectionner un cours') }}</option>
                                                @foreach ($cours as $cour)
                                                    <option value="{{ $cour->id }}">{{ $cour->nom }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="date_time" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Date et Heure') }}</label>
                                            <input type="datetime-local" name="date_time" id="date_time" class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                        </div>
                                        <div>
                                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">{{ __('Valider') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Affichage des étudiants et prise de présence -->
                        <div class="card shadow-lg border border-gray-200 rounded-lg">
                            <div class="card-body p-6">
                                <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                                    <h3 class="card-title text-lg font-semibold text-gray-700">
                                        {{ __('Cours') }} : {{ $cours->where('id', $selectedCoursId)->first()->nom ?? 'Inconnu' }} - {{ __('Date') }} : {{ \Carbon\Carbon::parse($selectedDateTime)->format('d/m/Y H:i') }}
                                    </h3>
                                    <div class="flex space-x-2 mt-2 md:mt-0">
                                        <span class="badge bg-green-500 text-white px-3 py-1 rounded-full">
                                            {{ __('Présents') }} : {{ $presentCount }}
                                        </span>
                                        <span class="badge bg-red-500 text-white px-3 py-1 rounded-full">
                                            {{ __('Absents') }} : {{ $absentCount }}
                                        </span>
                                        <span class="badge bg-blue-500 text-white px-3 py-1 rounded-full">
                                            {{ __('Total') }} : {{ $totalStudents }}
                                        </span>
                                    </div>
                                </div>
<hr>
                                <div class="mb-4 mt-[20px] flex flex-col md:flex-row md:items-center md:space-x-4">
                                    <a href="{{ route('presences.resetSelection') }}" class="inline-block px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition duration-150 ease-in-out">{{ __('Changer de sélection') }}</a>
                                    <div class="flex-1">
                                        <input type="text" id="searchInput" placeholder="{{ __('Rechercher par nom ou prénom...') }}" class="w-[400px] ml-[200px] px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                    </div>
                                    <form action="{{ route('presences.applyFilter') }}" method="POST" id="filterForm" class="flex items-center space-x-2">
                                        @csrf
                                        <label for="filter_type" class="text-sm font-medium text-gray-700">{{ __('') }}</label>
                                        <select name="filter_type" id="filter_type" class="px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out">
                                            <option value="tous" {{ $filterType === 'tous' ? 'selected' : '' }}>{{ __('Tous') }}</option>
                                            <option value="presents" {{ $filterType === 'presents' ? 'selected' : '' }}>{{ __('Présents') }}</option>
                                            <option value="absents" {{ $filterType === 'absents' ? 'selected' : '' }}>{{ __('Absents') }}</option>
                                        </select>
                                    </form>
                                </div>
<hr>
                                <div class="table-responsive custom-scrollbar" style="max-height: 350px; overflow-y: auto; mt-[20px]">
                                    <table class="table table-hover mb-0 w-full border-collapse" id="etudiantsTable">
                                        <thead class="bg-gray-200 text-gray-600 sticky top-0 z-10">
                                            <tr class = "text-xs">
                                                <th class="px-4 py-2 border-b">{{ __('Photo') }}</th>
                                                <th class="px-4 py-2 border-b">{{ __('Nom') }}</th>
                                                <th class="px-4 py-2 border-b">{{ __('Prénom') }}</th>
                                                <th class="px-4 py-2 border-b">{{ __('Actions') }}</th>
                                                <th class="px-4 py-2 border-b">{{ __('Statut') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="etudiantsBody">
                                            @foreach ($etudiants as $etudiant)
                                                @php
                                                    $presenceExists = \DB::table('presence')
                                                        ->where('etudiant_id', $etudiant->id)
                                                        ->where('cours_id', $selectedCoursId)
                                                        ->where('date', $selectedDateTime)
                                                        ->where('session_id', $sessionId)
                                                        ->exists();
                                                    $absenceExists = \DB::table('absence')
                                                        ->where('etudiant_id', $etudiant->id)
                                                        ->where('cours_id', $selectedCoursId)
                                                        ->where('date', $selectedDateTime)
                                                        ->where('session_id', $sessionId)
                                                        ->exists();
                                                @endphp
                                                <tr class="hover:bg-gray-50 transition duration-150 ease-in-out text-xs">
                                                    <td class="px-4 py-2 border-b">
                                                        <img src="{{ $etudiant->photo_url ?? '/images/default-avatar.png' }}" alt="Photo de {{ $etudiant->nom }}" class="rounded-full w-12 h-12 object-cover border border-gray-200" onerror="this.onerror=null;this.src='/images/default-avatar.png';">
                                                    </td>
                                                    <td class="px-4 py-2 border-b">{{ $etudiant->nom }}</td>
                                                    <td class="px-4 py-2 border-b">{{ $etudiant->prenom }}</td>
                                                    <td class="px-4 py-2 border-b">
                                                        <form action="{{ route('presences.storePresence') }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                                            <input type="hidden" name="cours_id" value="{{ $selectedCoursId }}">
                                                            <input type="hidden" name="date_time" value="{{ $selectedDateTime }}">
                                                            <input type="hidden" name="status" value="present">
                                                            <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out {{ $presenceExists || $absenceExists ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $presenceExists || $absenceExists ? 'disabled' : '' }}>{{ __('Présent') }}</button>
                                                        </form>
                                                        <form action="{{ route('presences.storePresence') }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                                            <input type="hidden" name="cours_id" value="{{ $selectedCoursId }}">
                                                            <input type="hidden" name="date_time" value="{{ $selectedDateTime }}">
                                                            <input type="hidden" name="status" value="absent">
                                                            <button type="submit" class="px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition duration-150 ease-in-out {{ $presenceExists || $absenceExists ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $presenceExists || $absenceExists ? 'disabled' : '' }}>{{ __('Absent') }}</button>
                                                        </form>
                                                        @if ($presenceExists || $absenceExists)
                                                            <form action="{{ route('presences.storePresence') }}" method="POST" style="display: inline;">
                                                                @csrf
                                                                <input type="hidden" name="etudiant_id" value="{{ $etudiant->id }}">
                                                                <input type="hidden" name="cours_id" value="{{ $selectedCoursId }}">
                                                                <input type="hidden" name="date_time" value="{{ $selectedDateTime }}">
                                                                <input type="hidden" name="status" value="cancel">
                                                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out" onclick="return confirm('{{ __('Annuler le statut de cet étudiant ?') }}')">{{ __('Annuler') }}</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2 border-b">
                                                        @if ($presenceExists)
                                                            <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                {{ __('Présent') }}
                                                            </span>
                                                        @elseif ($absenceExists)
                                                            <span class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                {{ __('Absent') }}
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                                                                {{ __('Non marqué') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            //---- Afficheo une notification SweetAlert si un message de succès, d'erreur ou d'info est présent
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '{{ __('Succès') }}',
                    text: '{{ session('success') }}',
                    confirmButtonText: '{{ __('OK') }}',
                    confirmButtonColor: '#2563eb',
                });
            @elseif (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('Erreur') }}',
                    text: '{{ session('error') }}',
                    confirmButtonText: '{{ __('OK') }}',
                    confirmButtonColor: '#dc2626',
                });
            @elseif (session('info'))
                Swal.fire({
                    icon: 'info',
                    title: '{{ __('Information') }}',
                    text: '{{ session('info') }}',
                    confirmButtonText: '{{ __('OK') }}',
                    confirmButtonColor: '#3b82f6',
                });
            @endif

            document.getElementById('filter_type').addEventListener('change', function () {
                document.getElementById('filterForm').submit();
            });

            // Recherche ao am côté client
            document.getElementById('searchInput').addEventListener('input', function () {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('#etudiantsBody tr');

                rows.forEach(row => {
                    const nom = row.cells[1].textContent.toLowerCase();
                    const prenom = row.cells[2].textContent.toLowerCase();

                    if (nom.includes(searchValue) || prenom.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>