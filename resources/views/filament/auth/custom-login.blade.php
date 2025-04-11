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

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}

    <!-- New right-side container -->
    <div class="right-side-container hidden lg:block">
        <!-- Add your image or content here -->
        <img 
        src="{{ asset('images/hero-right.jpg') }}" 
        alt="Right Side Image" 
        class="w-full h-full object-cover rounded-tl-lg rounded-bl-lg">
    </div>

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
            background: radial-gradient(circle, #d1f1e2, #deebfc, #fceed5, #daf7e1, #f6f8d8);
            background-size: 400% 400%;
            animation: gradient 5s linear infinite;
        }

        @media screen and (min-width: 1024px) {
            main {
                position: absolute; left: 125px;
            }

            main:before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle, #affdd9, #f2bcfd, #f7daa9, #acfabe, #f1f7a5);
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