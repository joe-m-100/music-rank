<button {{ $attributes->merge(['class' => "rounded-3xl bg-white text-black px-4 opacity-100 hover:opacity-80 hover:cursor-pointer font-semibold",
                                'type' => 'submit']) }}>
    {{ $slot }}
</button>