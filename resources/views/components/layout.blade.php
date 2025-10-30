<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MusicRank</title>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-black text-white font-mono">
    <div class="px-10">
        <nav class="bg-black px-10 flex justify-between items-center py-4 border-b border-white/20 z-10 w-full fixed top-0 lef-0 right-0">
            <div>
                <x-link href="/" class="font-bold text-2xl text-bright-green">MusicRank</x-link>
            </div>

            <div class="space-x-6">
                <x-link href="/search">Find an Album</x-link>
                <x-link href="/reviewed-albums">Reviewed Ablums</x-link>
                <x-link href="/global-statistics">Global Stats</x-link>
            </div>
        </nav>

        <main class="mt-25 max-w-5xl mx-auto">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
