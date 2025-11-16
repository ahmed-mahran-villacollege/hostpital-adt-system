<x-filament-widgets::widget class="fi-wi-admission">
    <x-filament::section>
        <div class="flex justify-end">
            <x-filament::button
                tag="a"
                href="{{ $this->getAdmissionFormUrl() }}"
                icon="heroicon-o-user-plus"
                wire:navigate
            >
                New Admission
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
