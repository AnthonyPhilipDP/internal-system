<nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
    <div class="flex flex-1 justify-start lg:gap-x-12">
        {{-- <a href="#" class="text-sm font-semibold text-gray-900">Product</a> --}}
        {{-- <a href="#" class="text-sm font-semibold text-gray-900">Features</a> --}}
        {{-- <a href="#" class="text-sm font-semibold text-gray-900">Marketplace</a> --}}
        {{-- <a href="#" class="text-sm font-semibold text-gray-900">Company</a> --}}
    </div>
    <div class="flex justify-center flex-none">
        <a href="#" class="-m-1.5 p-1.5">
            <span class="sr-only">PMSi</span>
            <img class="h-20 w-auto" src="{{ asset('images/PMSi Logo(transparent).png') }}" alt="">
        </a>
    </div>
    <div class="flex flex-1 justify-end">
        {{-- <a href="#" class="text-sm font-semibold text-gray-900">Log in <span aria-hidden="true">&rarr;</span></a> --}}
    </div>
    <div class="flex lg:hidden">
        <button id="menu-toggle" type="button" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
            <span class="sr-only">Open main menu</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>
    </div>
</nav>