<x-layout>
    <form method="POST" action="/review-save/{{ $album['id'] }}">
        @csrf
        <div class="flex flex-col justify-center items-center w-[80%] mx-auto relative" x-data="{ submit: false, index: 0, total: {{ count($album['tracks']) - 1 }} }">
            @foreach ($album['tracks'] as $key => $track)
                <div class="contents absolute"
                    x-show="index === {{ $key }}"
                >

                    <x-review-card title="{{ $track['name'] }}" image="{{ $album['image'] }}" :artists="$track['artists']" id="{{ $track['id'] }}"></x-review-card>
                </div>
            @endforeach

            <div class="w-[55%] mt-4 grid grid-cols-3 gap-2 items-center">
                <x-link-button class="text-sm bg-transparent text-bright-green mr-auto col-span-1 col-start-1"
                            x-show="index > 0"
                            @click="if (index > 0) index--"
                >
                    Back
                </x-link-button>

                <p class="mx-auto text-sm col-span-1 col-start-2" x-text="(index + 1) + ' of ' + (total + 1)"></p>

                <x-link-button class="text-sm bg-transparent text-bright-green ml-auto col-span-1 col-start-3"
                            @click="if (index < total) index++"
                            x-show="index < total"
                >
                    Next
                </x-link-button>

                <x-link-button class="text-sm bg-transparent text-white hover:border-white hover:text-white ml-auto col-span-1 col-start-3"
                            @click="submit = true"
                            x-show="index === total"
                >
                    Submit
                </x-link-button>
            </div>

            <div class="bg-black/50 backdrop-blur-xl shadow-md py-5 px-5 items-center
                          flex flex-col gap-20 justify-center w-[40%] absolute top-[15%] right-[30%] rounded-lg
                        "
                 x-show="submit"
                 @click.outside="submit = false"
            >
                <span class="font-semibold text-lg text-bright-green">Confirm Review?</span>

                <div class="flex justify-between w-full">
                    <div class="text-white font-bold hover:cursor-pointer py-1 px-3 hover:bg-red-500/40 rounded-xl"
                                @click="submit = false"
                    >
                        Cancel
                    </div>

                    <x-form-button>
                        Confirm
                    </x-form-button>
                </div>
            </div>
        </div>

        <input type="hidden" name="album" >
    </form>
</x-layout>
