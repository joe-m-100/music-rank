@props(['image' => 'https://placehold.co/640', 'id' => '#', 'rating' => '-'])

<a
    href="/analysis/{{ $id }}"
    class="
        group/album w-full bg-white/10 rounded-lg
        border-2 border-transparent hover:border-bright-green
        hover:cursor-pointer flex flex-col relative transition
        group-has-[a.group\/album:hover]/index:[&:not(:hover)]:opacity-70
        group-has-[a.group\/album:hover]/index:[&:not(:hover)]:scale-[0.97]
        "
>
    <img src={{ $image }} alt="" class="object-cover rounded-lg aspect-square pointer-events-none">

    <div
        class="
            bg-black/80 px-2.5 py-2 border-2 border-transparent
            _group-hover:border-bright-green text-bright-green text-xl
            absolute bottom-[5%] right-[5%] rounded-lg font-semibold
            "
        >
        {{ $rating }}
    </div>
</a>
