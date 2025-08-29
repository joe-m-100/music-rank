@props(['title' => 'TRACK_NAME', 'artists' => ['ARTIST_NAME', 'FEATURED_ARTIST'], 'duration' => 0])

<?php
    $artists_text = '';
    foreach ($artists as $artist) {
        $artists_text .= $artist;

        $artists_text .= ', ';
    }

    $contributors = rtrim($artists_text, ', ');

    $mins = (int) ($duration / 60);
    $secs = round($duration % 60);
    $formatted_duration = sprintf('%01d:%02d', $mins, $secs);
?>

<div class="group bg-white/10 rounded-lg border-2 border-transparent hover:border-bright-green hover:bg-white/20">
    <div class="px-4 py-2 flex gap-2 items-center">
        <div class="w-[50%]">
            <h2 class="font-semibold text-[16px] overflow-hidden overflow-ellipsis text-nowrap">{{ html_entity_decode($title) }}</h2>
        </div>

        <div class="w-[50%] font-normal text-white/75 text-[12px] flex justify-between">
            <div class="w-[70%]">
                <p class="font-normal text-white/75 overflow-hidden overflow-ellipsis text-nowrap">{{ $contributors }}</p>
            </div>
            <p>{{ $formatted_duration }}</p>
        </div>
    </div>
</div>
