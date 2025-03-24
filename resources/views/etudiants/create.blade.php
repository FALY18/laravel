<!-- resources/views/etudiants/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-gray-800 text-center">
            👨‍🎓 {{ __('Ajouter un Étudiant') }}
        </h2>
    </x-slot>

    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="w-full max-w-3xl bg-white shadow-2xl rounded-lg p-8 transition-all transform hover:scale-105">
            <h1 class="text-center text-3xl font-bold text-blue-600 mb-6">
                {{ __('Nouvel Étudiant') }}
            </h1>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg shadow">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>⚠️ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('etudiants.store') }}" enctype="multipart/form-data" id="etudiantForm" class="space-y-6">
                @csrf

                <!-- Nom -->
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nom') }}</label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                           class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('nom') border-red-500 @enderror">
                    @error('nom')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prénom -->
                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Prénom') }}</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required
                           class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out @error('prenom') border-red-500 @enderror">
                    @error('prenom')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo -->
                <div>
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Photo (optionnel)') }}</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 cursor-pointer transition">
                        <input type="file" id="photo" name="photo" accept="image/*" class="hidden">
                        <p class="text-gray-500">📷 Glissez-déposez une image ou cliquez pour sélectionner un fichier</p>
                        <img id="photoPreview" class="hidden mx-auto mt-3 w-36 h-36 rounded-lg shadow border border-gray-200 object-cover" alt="Aperçu de la photo">
                    </div>
                </div>

                <!-- Boutons Ajouter & Annuler -->
                <div class="flex justify-center space-x-4">
                    <button type="submit"
                            class="flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-700 text-white font-semibold rounded-lg shadow-md hover:from-green-700 hover:to-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all transform hover:scale-105 active:scale-95">
                        ✅ {{ __('Ajouter') }}
                    </button>

                    <a href="{{ route('etudiants.create') }}"
                       class="flex items-center px-6 py-3 bg-gray-500 text-white font-semibold rounded-lg shadow-md bg-bl-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all transform hover:scale-105 active:scale-95">
                        ❌ {{ __('Annuler') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Script pour l'aperçu de l'image -->
    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const photoInput = document.getElementById('photo');
                const preview = document.getElementById('photoPreview');

                if (photoInput) {
                    photoInput.addEventListener('change', function (event) {
                        const file = event.target.files[0];

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (e) {
                                preview.src = e.target.result;
                                preview.classList.remove('hidden');
                            };
                            reader.readAsDataURL(file);
                        } else {
                            preview.src = '';
                            preview.classList.add('hidden');
                        }
                    });

                    // Drag & Drop
                    const dropZone = photoInput.closest('.border-2');
                    dropZone.addEventListener("dragover", (e) => {
                        e.preventDefault();
                        dropZone.classList.add("border-blue-500");
                    });

                    dropZone.addEventListener("dragleave", () => {
                        dropZone.classList.remove("border-blue-500");
                    });

                    dropZone.addEventListener("drop", (e) => {
                        e.preventDefault();
                        dropZone.classList.remove("border-blue-500");
                        photoInput.files = e.dataTransfer.files;
                        photoInput.dispatchEvent(new Event("change"));
                    });

                    dropZone.addEventListener("click", () => {
                        photoInput.click();
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
