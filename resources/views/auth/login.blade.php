<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Connexion</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-100 px-4">

    <main class="w-full max-w-md rounded-lg bg-white p-8 shadow-md">
        <h1 class="mb-6 text-center text-2xl font-bold text-gray-800">
            Connexion
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

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

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
                    autofocus
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
                    autocomplete="current-password"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    value="1"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600"
                >

                <label for="remember" class="ml-2 text-sm text-gray-600">
                    Se souvenir de moi
                </label>
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700"
            >
                Se connecter
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Vous n'avez pas encore de compte ?
            <a
                href="{{ route('register') }}"
                class="font-medium text-blue-600 hover:underline"
            >
                Créer un compte
            </a>
        </p>
    </main>

</body>
</html>
