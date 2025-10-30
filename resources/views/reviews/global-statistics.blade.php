<x-layout>
    <h1 class=" text-[27px] font-bold mb-10">{{ $heading }}</h1>

    <div class="flex flex-col">
        @foreach ($stats as $stat)
            <div class="py-2 flex justify-between text-lg">
                <div class="text-white">
                    {{ $stat['title'] }}
                </div>

                <div class="text-white/75">
                    {{ $stat['value'] }}
                </div>
            </div>

            <hr class="border-t border-white/50 last:hidden my-5" />
        @endforeach
    </div>
</x-layout>
