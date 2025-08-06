<x-layout>
        <div class="max-w-[60%] mx-auto flex flex-col items-center">
            <div class="text-center mb-10 text-[27px] font-bold">
                <h1>Artist Search</h1>
            </div>

            <form method="GET" action="/search" class="w-full">
                <div class="flex gap-5 w-full">
                    <x-form-input name="artist" id="artist" required placeholder="Enter Artist Name..." />

                    <x-form-button>Search</x-form-button>
                </div>
            </form>
        </div>
</x-layout>