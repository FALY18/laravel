<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">
            {{ __('Créer un activité ou cours') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900">
                    <h1 class="text-center text-2xl font-bold text-blue-600 mb-6">
                        {{ __('Nouveaux activité/cours') }}
                    </h1>

                    <form method="POST" action="{{ route('cours.store') }}">
                        @csrf

                        <!-- Nom du cours -->
                        <div class="mb-4">
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Nom du Cours') }}
                            </label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 @error('nom') border-red-500 @enderror">
                            @error('nom')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Saisie de la description -->
                        <div class="mb-4">
                            <label for="descriptionInput" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Saisissez la description du cours') }}
                            </label>
                            <input type="text" id="descriptionInput"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150"
                                   placeholder="Écrivez ici..." oninput="updatePreview()">
                        </div>

                        <!-- Aperçu de la description -->
                        <div class="mb-4 p-3 bg-gray-100 rounded-md border border-gray-300">
                            <h3 class="font-semibold text-gray-700 mb-2">-- </h3>
                            <p id="preview" class="text-gray-600 italic">votre desciption</p>
                        </div>

                        <!-- Champ caché pour envoyer la description -->
                        <input type="hidden" id="description" name="description" value="{{ old('description') }}">

                        <!-- Boutons -->
                        <div class="flex space-x-40 items-center mt-4">
                            <a href="{{ route('cours.create') }}"
                               class="px-4 py-2 bg-gray-500 text-white text-sm font-semibold rounded-md shadow-md hover:bg-gray-600 transition duration-150">
                                 {{ __('Annuler') }}
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md shadow-md hover:bg-green-700 transition duration-150">
                                 {{ __('Ajouter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



	@push('scripts')
        <script>
            function updatePreview() {
                let inputText = document.getElementById('descriptionInput').value;
                let preview = document.getElementById('preview');
                let hiddenInput = document.getElementById('description');

                if (inputText.trim() === "") {
                    preview.innerText = "Votre description apparaîtra ici...";
                    preview.classList.add("italic", "text-gray-600");
                } else {
                    preview.innerText = inputText;
                    preview.classList.remove("italic", "text-gray-600");
                }

                hiddenInput.value = inputText;
            }

            document.addEventListener("DOMContentLoaded", function () {
                updatePreview();

                // Alertes en cas na erreur serveur ou erreurs de validation**
                @if (session('error'))
                    alert("Erreur\n\n{{ session('error') }}");
                @elseif ($errors->any())
                    alert("Erreur de validation\n\n{!! implode('\n', array_map('addslashes', $errors->all())) !!}");
                @endif
            });
        </script>
    @endpush
</x-app-layout>