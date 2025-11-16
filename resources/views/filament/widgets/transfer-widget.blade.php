<x-filament-widgets::widget class="fi-wi-transfer">
    <x-filament::section>
        <div class="flex justify-end">
            <x-filament::button
                tag="a"
                href="{{ $this->getTransferFormUrl() }}"
                icon="heroicon-o-arrow-right-on-rectangle"
                color="warning"
                wire:navigate
            >
                Transfer Patient
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
