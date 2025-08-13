<x-layout>
    <?php $temp = [['title' => 'One'], ['title' => 'Two'], ['title' => 'Three']] ?>
    <div class="flex flex-col justify-center items-center w-[80%] mx-auto relative" x-data="{ index: 0, total: 2 }">
        @foreach ($temp as $key => $item)
            <div class="contents absolute"
                 x-show="index === {{ $key }}"
            >

                <x-review-card title="{{ $item['title'] }}"></x-review-card>
            </div>
        @endforeach

        <div class="w-[55%] mt-4 flex justify-between">
            <x-link-button class="text-sm bg-transparent text-bright-green mr-auto"
                           x-show="index !== 0"
                           @click="if (index > 0) index--"
            >
                Back
            </x-link-button>

            <x-link-button class="text-sm bg-transparent text-bright-green ml-auto"
                           @click="if (index < total) index++"
                           x-show="index < total"
            >
                Next
            </x-link-button>
        </div>
    </div>
</x-layout>