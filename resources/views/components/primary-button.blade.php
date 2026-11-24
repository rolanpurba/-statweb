<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-sw-navy border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sw-navy-light focus:bg-sw-navy-light active:bg-sw-navy-dark focus:outline-none focus:ring-2 focus:ring-sw-gold focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm']) }}>
    {{ $slot }}
</button>
