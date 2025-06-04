<x-filament-panels::page.simple>
  @if (filament()->hasRegistration())
    <x-slot name="subheading">
      {{ __('filament-panels::pages/auth/login.actions.register.before') }}

      {{ $this->registerAction }}
    </x-slot>
  @endif

  {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}

  <x-filament-panels::form id="form" wire:submit="authenticate">
    {{ $this->form }}

    <x-filament-panels::form.actions :actions="$this->getCachedFormActions()" :full-width="$this->hasFullWidthFormActions()" />
  </x-filament-panels::form>

  {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

  <!-- New right-side container -->
  {{-- <div class="right-side-container hidden lg:block">
        <!-- Add your image or content here -->
        <img 
        src="{{ asset('images/hero-right.jpg') }}" 
        alt="Right Side Image" 
        class="w-full h-full object-cover rounded-tl-lg rounded-bl-lg">
    </div> --}}

  <style>
    @keyframes gradient {
      0% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0% 50%;
      }
    }

    body {
      background: radial-gradient(circle, #f1d1d1, #fcdef7, #fcdabb, #f0b9b9, #f8dede);
      background-size: 300% 300%;
      animation: gradient 15s linear(0 0%, 0 1.8%, 0.01 3.6%, 0.03 6.35%, 0.07 9.1%, 0.13 11.4%, 0.19 13.4%, 0.27 15%, 0.34 16.1%, 0.54 18.35%, 0.66 20.6%, 0.72 22.4%, 0.77 24.6%, 0.81 27.3%, 0.85 30.4%, 0.88 35.1%, 0.92 40.6%, 0.94 47.2%, 0.96 55%, 0.98 64%, 0.99 74.4%, 1 86.4%, 1 100%) infinite;
    }

    @media screen and (min-width: 1024px) {
      main {
        position: absolute;
        /* left: 125px; */
      }

      main:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle, #fdafaf, #ff4a4a, #949494, #facbac, #ff3f3f);
        background-size: 300% 300%;
        animation: gradient 10s linear infinite;
        border-radius: 12px;
        z-index: -9;
        transform: rotate(-7deg);
      }

      #custom {
        position: fixed;
        right: 100px;
        color: #ffffff;
        font-size: 2em;
        font-weight: bold;
        text-shadow: #3f6212 2px 2px 5px;
      }

      /* New right-side container styling */
      .right-side-container {
        position: fixed;
        top: 0;
        right: 0;
        width: 60%;
        height: 100%;
        box-sizing: border-box;
      }
    }
  </style>
</x-filament-panels::page.simple>
