<x-layout>
    <div class="grid grid-cols-4 gap-5">
        @foreach ($albums as $album)
            <div class="col-span-1">
                <x-album-summary-card image="{{ $album['image'] }}" rating="{{ $album['rating'] }}" id="{{ $album['id'] }}" />
            </div>
        @endforeach
    </div>
</x-layout>
