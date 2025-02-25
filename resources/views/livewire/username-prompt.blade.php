<div>
    @if($isOpen)
    <div class="fixed inset-0 bg-gray-800 bg-opacity-75 transition-opacity z-50"></div>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-900 px-6 pb-6 pt-8 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-8">
                    <x-filament::modal>
                    <div class="flex justify-center mb-4">
                        <x-filament::icon icon="heroicon-o-information-circle"/>
                    </div>
                </x-filament::modal>
                    <div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg font-bold leading-7 text-gray-900 dark:text-white">
                                Hey there, {{ $name }}!
                            </h3>
                            <h3 class="text-lg font-bold leading-7 text-gray-900 dark:text-white">
                                It looks like your username hasn't been set up yet.
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    Would you like to set up your username now?
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    You can always do this later in your profile settings.
                                </p>
                            </div>
                        </div>
                    </div>
                    <form wire:submit="save" class="mt-6 sm:mt-8">
                        {{ $this->form }}

                        <div class="mt-6 sm:mt-8 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-4">
                            <x-filament::button type="submit" class="me-2 mb-2">
                                Save
                            </x-filament::button>
                            <x-filament::button type="button" wire:click="skip" color="info" class="mt-3 sm:col-start-1 sm:mt-0">
                                Do this later
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>