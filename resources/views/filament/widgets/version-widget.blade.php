<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <a
                    href="http://localhost:8000/"
                    rel="noopener noreferrer"
                    target="_blank"
                    class="font-bold"
                >
                    Precision Measurement Specialists,<span class="italic text-red-500"> i</span>nc. - Internal Website
                </a>

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    v0.1.0
                </p>
            </div>

            <div class="flex flex-col items-end gap-y-1">
                <x-filament::link
                    wire:navigate
                    color="gray"
                    href="/release-notes"
                    icon="heroicon-m-book-open"
                    icon-alias="panels::widgets.filament-info.open-documentation-button"
                >
                    <span class="text-red-500">{{ __('Release Notes') }}</span>
                </x-filament::link>

                <x-filament::link
                    color="gray"
                    href=""
                    icon="heroicon-m-shield-check"
                >
                <span class="text-red-500">{{ __('Alpha Test') }}</span>
                </x-filament::link>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
