@props(['image' => 'https://placehold.co/640', 'id' => '#', 'rating' => '-'])

<div class="group w-full bg-white/10 rounded-lg border-2 border-transparent hover:border-bright-green hover:cursor-pointer flex flex-col relative">
    <img src={{ $image }} alt="" class="object-cover rounded-lg aspect-square pointer-events-none">

    <div
        class="
            bg-black/80 px-2.5 py-2 border-2 border-transparent
            group-hover:border-bright-green text-bright-green text-xl
            absolute bottom-[5%] right-[5%] rounded-lg font-semibold
            "
        >
        {{ $rating }}
    </div>
</div>
