<x-layout>
    <form method="POST" action="/" onsubmit="return confirm('Submit review?');">
        <div class="flex flex-col justify-center items-center w-[80%] mx-auto relative" x-data="{ index: 0, total: {{ count($album['tracks']) - 1 }} }">
            @foreach ($album['tracks'] as $key => $track)
                <div class="contents absolute"
                    x-show="index === {{ $key }}"
                >

                    <x-review-card title="{{ $track['name'] }}" image="{{ $album['image'] }}" :artists="$track['artists']"></x-review-card>
                </div>
            @endforeach

            <div class="w-[55%] mt-4 flex justify-between items-center">
                <x-link-button class="text-sm bg-transparent text-bright-green mr-auto"
                            x-show="index > 0"
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

                <x-form-button class="ml-auto py-1.5" x-show="index === total">
                    Submit
                </x-form-button>
            </div>
        </div>
    </form>
</x-layout>