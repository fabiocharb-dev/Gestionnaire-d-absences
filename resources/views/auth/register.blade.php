<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inscription</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-100 px-4">

    <main class="w-full max-w-md rounded-lg bg-white p-8 shadow-md">
        <h1 class="mb-6 text-center text-2xl font-bold text-gray-800">
            Créer un compte
        </h1>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-100 p-4 text-sm text-red-700">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="nom" class="mb-1 block text-sm font-medium text-gray-700">
                    Nom
                </label>

                <input
                    id="nom"
                    type="text"
                    name="nom"
                    value="{{ old('nom') }}"
                    required
                    autofocus
                    autocomplete="family-name"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('nom')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="prenom" class="mb-1 block text-sm font-medium text-gray-700">
                    Prénom
                </label>

                <input
                    id="prenom"
                    type="text"
                    name="prenom"
                    value="{{ old('prenom') }}"
                    required
                    autocomplete="given-name"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('prenom')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="genre" class="mb-1 block text-sm font-medium text-gray-700">
                    Genre
                </label>

                <select
                    id="genre"
                    name="genre"
                    required
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    <option value="">Sélectionner</option>
                    <option value="homme" @selected(old('genre') === 'homme')>Homme</option>
                    <option value="femme" @selected(old('genre') === 'femme')>Femme</option>
                    <option value="nonbinaire" @selected(old('genre') === 'nonbinaire')>Non binaire</option>
                </select>

                @error('genre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="mb-1 block text-sm font-medium text-gray-700">
                    Type de compte
                </label>

                <select
                    id="role"
                    name="role"
                    required
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >
                    <option value="">Sélectionner</option>
                    <option value="utilisateur" @selected(old('role') === 'utilisateur')>Utilisateur</option>
                    <option value="admin" @selected(old('role') === 'admin')>Administrateur</option>
                </select>

                @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
                    Adresse email
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-gray-700">
                    Mot de passe
                </label>

                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">
                    Confirmation du mot de passe
                </label>

                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700"
            >
                S'inscrire
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Vous avez déjà un compte ?
            <a
                href="{{ route('login') }}"
                class="font-medium text-blue-600 hover:underline"
            >
                Se connecter
            </a>
        </p>
    </main>

</body>
</html>
