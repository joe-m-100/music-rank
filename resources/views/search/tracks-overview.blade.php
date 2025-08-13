<x-layout>
    <div>
        <div class="flex justify-between items-center  mb-10 ">
            <h1 class=" text-[27px] font-bold">{{ $heading }}</h1>

            <x-link-button>Review</x-link-button>
        </div>

        <div class="flex flex-col gap-5">
            @foreach ($album['tracks'] as $track)
                <x-track-overview-card title="{{ $track['name'] }}" :artists="$track['artists']" :duration="$track['duration']" />
            @endforeach
        </div>
    </div>
</x-layout>