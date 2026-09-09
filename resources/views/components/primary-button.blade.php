<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#00b87d] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008c63] focus:bg-[#008c63] active:bg-[#00704f] focus:outline-none focus:ring-2 focus:ring-[#00b87d] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
