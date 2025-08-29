<x-layout>
    <div>
        <div class="flex justify-between items-center  mb-10 ">
            <h1 class=" text-[27px] font-bold">{{ $heading }}</h1>

            <form method="POST" action="/review?album={{  $album['id'] }}">
                @csrf
                <input type="hidden" name="data" id="data" value='{{ json_encode($album) }}'>
                <x-form-button class="py-1.5" href="/review">Review</x-form-button>
            </form>
        </div>

        <div class="flex flex-col gap-5">
            @foreach ($album['tracks'] as $track)
                <x-track-overview-card title="{{ $track['name'] }}" :artists="$track['artists']" :duration="$track['duration']" />
            @endforeach
        </div>
    </div>
</x-layout>