@props(['title' => 'Unknown', 'subtext' => 'blank', 'image' => 'https://placehold.co/640', 'id' => '#', 'type' => 'none'])

<?php
    if ($type === 'Artist') {
        $link = '/search/' . $id;
    }
    else {
        $link = '#';
    }

?>

<div class="group bg-white/10 rounded-lg border-2 border-transparent hover:border-bright-green hover:cursor-pointer">
    <a href={{ $link }} class="flex flex-col items-center">
        <img src={{ $image }} alt="" class="w-full aspect-square object-cover p-4">

        <div class="w-full p-4">
            <h2 class="text-[17px] font-bold group-hover:text-bright-green overflow-hidden overflow-ellipsis text-nowrap">{{ $title }}</h2>

            <p class="text-white/75">{{ $subtext }}</p>
        </div>
    </a>
</div>