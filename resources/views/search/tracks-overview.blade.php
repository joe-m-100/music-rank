<x-layout>
    <div>
        <h1>{{ $heading }}</h1>

        <div class="flex flex-col gap-5">
            @foreach ($album['tracks'] as $track)
                <x-track-overview-card title="{{ $track['name'] }}" :artists="$track['artists']" :duration="$track['duration']" />
            @endforeach
        </div>
    </div>
</x-layout>