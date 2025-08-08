<x-layout>
    <div class="text-center mb-10 text-[27px] font-bold">
        <h1>{{ $heading }}</h1>
    </div>

    <div class="grid grid-cols-10 gap-5 w-full">
    @foreach ($results as $result)
        <div class="col-span-2">
            @if ($result['image'])
                <x-results-card title="{{ $result['name'] }}" subtext="{{ $result['type'] }}" type="{{ $result['type'] }}" id="{{ $result['id'] }}" image="{{ $result['image'] }}">
                </x-results-card>
            @else
                <x-results-card title="{{ $result['name'] }}" subtext="{{ $result['type'] }}" type="{{ $result['type'] }}" id="{{ $result['id'] }}">
                </x-results-card>
            @endif
        </div>
    @endforeach
    </div>
</x-layout>