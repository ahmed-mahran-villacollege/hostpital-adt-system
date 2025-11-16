<x-filament-widgets::widget class="fi-wi-discharge">
    <x-filament::section>
        <div class="flex justify-end">
            <x-filament::button
                tag="a"
                href="{{ $this->getDischargeFormUrl() }}"
                icon="heroicon-o-user-minus"
                color="danger"
                wire:navigate
            >
                Discharge Patient
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
