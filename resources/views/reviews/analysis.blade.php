@php
    $album_length = $tracks->count();
@endphp


<x-layout>
    <h1 class=" text-[34px] font-bold mb-10">{{ $heading }}</h1>

    <div class="grid grid-cols-6 gap-3">
        <div class="col-start-1 col-span-4 row-span-2 border border-white/75 hover:border-white rounded-lg">
            <script src="https://d3js.org/d3.v7.min.js"></script>

            <script>
                window.lineChartData = @json($line_chart_data);
            </script>

            @vite('resources/js/line_chart_script.js')

            <div
                id="lineChartContainer"
                class="
                    px-3 py-4 bg-white/10 aspect-video
                    w-full
                    "
            >
            </div>
        </div>

        <div
            class="
                col-span-2 row-span-1
                px-4 py-2 bg-white/10 rounded-lg aspect-video
                border border-white/75 hover:border-white
                "
        >
            <div class="font-semibold text-lg mb-2">
                Core Stats
            </div>

            @foreach ($core_stats as $stat)
                <div class="flex justify-between">
                    {{ $stat['name'] }} <span class="text-white/75">{{ $stat['value'] }}</span>
                </div>
            @endforeach
        </div>

        <div
            class="
                col-span-2 row-span-1
                px-4 py-2 bg-white/10 rounded-lg aspect-video
                border border-white/75 hover:border-white
                "
        >
            <div class="font-semibold text-lg mb-5">
                Top Tracks
            </div>

            <ol class="flex flex-col gap-3">
                @foreach ($top_tracks as $n => $track)
                    <li class="text-white/80 first:text-white last:text-white/70 line-clamp-1">
                        {{ ($n + 1) . '. ' . $track['name'] }}
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="col-start-3 col-span-4 row-span-2">
            <script>
                window.chartData = @json($bar_chart_data);
            </script>

            @vite('resources/js/bar_chart_script.js')

            <div
                id="barChartContainer"
                class="
                    px-3 py-4 bg-white/10 rounded-lg aspect-video
                    border border-white/75 hover:border-white w-full
                    "
            >
            </div>
        </div>

        <div
            class="
                col-start-1 col-span-1 row-start-3 row-span-1
                bg-white/10 rounded-lg overflow-clip relative
                border border-white/75 hover:border-white
                "
        >
            <div class=" w-full rounded-lg">
                <img
                    src="{{ $artist['image'] }}"
                    alt="Artist Image"
                    class="aspect-auto object-cover object-center h-full"
                >
            </div>
        </div>

        <div
            class="
                col-start- col-span-1 row-start-3 row-span-1
                bg-white/10 rounded-lg py-2 px-4
                border border-white/75 hover:border-white
                "
        >
            <div class="font-semibold text-lg mb-5">
                Features
            </div>
        </div>

        <div
            class="
                col-start-1 col-span-2 py-2 px-4
                bg-white/10 rounded-lg h-full
                border border-white/75 hover:border-white
                "
        >
            <div class="font-semibold text-lg mb-3">
                {{ $artist['name'] }}
            </div>

            @foreach ($artist['stats'] as $stat)
                <div class="flex justify-between line-clamp-1">
                    {{ $stat['name'] }} <span class="text-white/75 ml-2 whitespace-nowrap overflow-hidden text-ellipsis">{{ $stat['value'] }}</span>
                </div>
            @endforeach
        </div>

    </div>

</x-layout>
