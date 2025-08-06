<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MusicRank</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-black text-white font-mono">
    <div class="px-10">
        <nav class="flex justify-between items-center py-4 border-b border-white/20">
            <div>
                <x-link href="/" class="font-bold text-2xl text-bright-green">MusicRank</x-link>
            </div>

            <div class="space-x-6">
                <x-link href="/search">Find an Album</x-link>
                <x-link href="/">Reviewed Ablums</x-link>
                <x-link href="/">Global Stats</x-link>
            </div>
        </nav>

        <main class="mt-10 max-w-5xl mx-auto">
            {{ $slot }}
        </main>
    </div>
</body>

</html>