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
                    Precision Measurement Specialists,<span class="italic"> inc.</span> - Internal Website
                </a>

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    v0.1.0
                </p>
            </div>

            <div class="flex flex-col items-end gap-y-1">
                <x-filament::link
                    color="gray"
                    href="/release-notes"
                    icon="heroicon-m-book-open"
                    icon-alias="panels::widgets.filament-info.open-documentation-button"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    {{ __('Release Notes') }}
                </x-filament::link>

                <x-filament::link
                    color="gray"
                    href=""
                    icon="heroicon-m-shield-check"
                >
                    {{ __('Alpha Test') }}
                </x-filament::link>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
