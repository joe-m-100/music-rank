<x-layout>
    <div class="text-center mb-10 text-[27px] font-bold">
        <h1>Search Results for "{{ $query }}"</h1>
    </div>

    <div class="grid grid-cols-10 gap-5 w-full">
    @foreach ($results as $result)
        <div class="col-span-2">
            @if ($result['images'])
                <x-results-card title="{{ $result['name'] }}" subtext="Artist" image="{{ $result['images'][0]['url'] }}">
                </x-results-card>
            @else
                <x-results-card title="{{ $result['name'] }}" subtext="Artist">
                </x-results-card>
            @endif
        </div>
    @endforeach
    </div>

</x-layout>