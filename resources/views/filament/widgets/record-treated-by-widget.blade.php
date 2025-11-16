<x-filament-widgets::widget class="fi-wi-treated-by">
    <x-filament::section>
        <div class="flex justify-end">
            <x-filament::button
                tag="a"
                href="{{ $this->getRecordTreatedByFormUrl() }}"
                icon="heroicon-o-clipboard-document-check"
                color="success"
                wire:navigate
            >
                Record Treated By
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
