<header class="absolute inset-x-0 top-0 z-50">

  @include('components.layouts.partials.navbar')

  <div id="mobile-menu" class="hidden lg:hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 z-50"></div>
    <div
      class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
      <div class="flex items-center justify-between">
        <a wire:navigate href="/" class="-m-1.5 p-1.5">
          <span class="sr-only">PMSi</span>
          <img class="h-20 w-auto" src="{{ asset('images/PMSi Logo(transparent).png') }}" alt="">
        </a>
        <button id="menu-close" type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700">
          <span class="sr-only">Close menu</span>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="mt-6 flow-root">
        <div class="-my-6 divide-y divide-gray-500/10">
          <div class="space-y-2 py-6">
            {{-- <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">Product</a> --}}
            {{-- <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">Features</a> --}}
            {{-- <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">Marketplace</a> --}}
            {{-- <a href="#" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold text-gray-900 hover:bg-gray-50">Company</a> --}}
          </div>
          <div class="py-6">
            <a href="/admin"
              class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-semibold text-gray-900 hover:bg-gray-50">Administration</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
