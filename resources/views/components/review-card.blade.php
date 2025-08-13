@props(['title' => 'TITLE', 'artists' => '[]'])

<div class="flex flex-col bg-white/10 p-6 rounded-2xl w-[55%] gap-6">
    <img src="https://placehold.co/600x600" class="aspect-square object-cover">

    <div class="mt-5">
        <h2 class="text-nowrap text-ellipsis overflow-hidden font-semibold text-lg">{{ html_entity_decode($title) }}</h2>
        <p class="text-nowrap text-ellipsis overflow-hidden text-white/75">Artists</p>
    </div>

    <div class="grid grid-cols-10 gap-2 justify-between" x-data="{ selected: '5' }">
        @for ($i = 1; $i <= 10; $i++)
            <label class="cursor-pointer col-span-1">
                <input type="radio" name="score" value="{{ $i }}" x-model="selected" class="sr-only peer">

                <span class="text-sm flex items-center justify-center p-1 border rounded-sm"
                      :class="selected == '{{ $i }}' ? 'bg-bright-green border-bright-green text-black hover:text-black' : 'bg-transparent border-white/75 hover:border-bright-green hover:text-bright-green'">
                    {{ $i }}
                </span>
            </label>
        @endfor
    </div>
</div>