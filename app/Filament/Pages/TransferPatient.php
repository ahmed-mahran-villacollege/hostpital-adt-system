<?php

namespace App\Filament\Pages;

use App\Models\Admission;
use App\Models\Ward;
use App\Support\Concerns\ValidatesWardAssignment;
use App\Support\WardCapacityFormatter;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions as ActionsLayout;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransferPatient extends Page
{
    use ValidatesWardAssignment;

    protected static ?string $title = 'Transfer Patient';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'transfer';

    public ?array $data = [];

    /**
     * @var array<int, Admission|null>
     */
    protected array $admissionCache = [];

    /**
     * @var array<int, Ward|null>
     */
    protected array $wardCache = [];

    protected ?array $wardOptions = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transfer details')
                    ->description('Select a currently admitted patient and choose the destination ward.')
                    ->columns(2)
                    ->schema([
                        Select::make('admission_id')
                            ->label('Patient')
                            ->placeholder('Search admitted patients...')
                            ->searchable()
                            ->live()
                            ->getSearchResultsUsing(fn (string $search): array => $this->searchAdmissions($search))
                            ->getOptionLabelUsing(fn ($value): ?string => $this->getAdmissionOptionLabel($value))
                            ->required(),
                        Select::make('destination_ward_id')
                            ->label('Destination ward')
                            ->options(fn (): array => $this->getWardOptions())
                            ->live()
                            ->searchable()
                            ->required()
                            ->helperText('Only wards that match the type and have free beds will pass validation.'),
                        Placeholder::make('current_ward')
                            ->label('Current ward:')
                            ->content(fn (callable $get): string => $this->getCurrentWardDisplay($get('admission_id'))),
                        Placeholder::make('destination_capacity')
                            ->label('Ward occupancy:')
                            ->content(fn (callable $get): string => WardCapacityFormatter::forWardId($get('destination_ward_id'))),
                    ])->columnSpanFull()->columns(2),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    protected function getFormContentComponent(): Component
    {
        return Form::make([
            EmbeddedSchema::make('form'),
        ])
            ->id('transfer-form')
            ->livewireSubmitHandler('transfer')
            ->footer([
                $this->getFormActionsContentComponent(),
            ]);
    }

    protected function getFormActionsContentComponent(): Component
    {
        return ActionsLayout::make($this->getFormActions())
            ->alignment($this->getFormActionsAlignment())
            ->fullWidth(false)
            ->key('form-actions');
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('transfer')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('primary')
                ->submit('transfer')
                ->requiresConfirmation()
                ->modalHeading('Confirm transfer')
                ->modalDescription(fn (): string => $this->getConfirmationMessage())
                ->disabled(fn (): bool => blank($this->data['admission_id'] ?? null) || blank($this->data['destination_ward_id'] ?? null)),
        ];
    }

    public function transfer(): void
    {
        $this->callHook('beforeValidate');
        $data = $this->form->getState();
        $this->callHook('afterValidate');

        $admission = $this->getAdmission((int) ($data['admission_id'] ?? 0));

        if (! $admission) {
            Notification::make()
                ->title('Select a patient')
                ->body('Choose an admitted patient to continue.')
                ->danger()
                ->send();

            return;
        }

        $destinationWardId = (int) ($data['destination_ward_id'] ?? 0);

        if ($admission->ward_id === $destinationWardId) {
            Notification::make()
                ->title('Already in that ward')
                ->body('Select a different ward to transfer the patient.')
                ->warning()
                ->send();

            return;
        }

        $this->validateWardAssignment(
            $destinationWardId,
            $admission->patient?->sex,
            'destination_ward_id',
        );

        $admission->update([
            'ward_id' => $destinationWardId,
        ]);

        unset($this->admissionCache[$admission->getKey()]);

        $admission->refresh()->load(['patient', 'ward']);

        Notification::make()
            ->success()
            ->title('Patient transferred')
            ->body(
                sprintf(
                    '%s has been transferred to %s.',
                    $admission->patient?->name ?? 'The patient',
                    $admission->ward?->name ?? 'the selected ward',
                ),
            )
            ->send();

        $this->admissionCache = [];
        $this->form->fill();
    }

    /**
     * @return array<int, string>
     */
    protected function searchAdmissions(string $search): array
    {
        return Admission::query()
            ->with(['patient', 'ward'])
            ->whereHas('patient', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('hospital_number', 'like', "%{$search}%");
            })
            ->orderByDesc('admitted_at')
            ->limit(25)
            ->get()
            ->mapWithKeys(fn (Admission $admission): array => [
                $admission->getKey() => $this->formatAdmissionOption($admission),
            ])
            ->all();
    }

    protected function getAdmissionOptionLabel(mixed $value): ?string
    {
        if (! $value) {
            return null;
        }

        return $this->formatAdmissionOption(
            $this->getAdmission((int) $value),
        );
    }

    protected function formatAdmissionOption(?Admission $admission): ?string
    {
        if (! $admission) {
            return null;
        }

        $patient = $admission->patient;
        $ward = $admission->ward;

        return trim(
            collect([
                $patient?->name,
                $patient ? '('.$patient->hospital_number.')' : null,
            ])->filter()->implode(' '),
        );
    }

    protected function getCurrentWardDisplay(mixed $admissionId): string
    {
        $admission = $this->getAdmission((int) $admissionId);

        if (! $admission?->ward) {
            return 'Select a patient to view their current ward.';
        }

        return sprintf(
            '%s (%s)',
            $admission->ward->name,
            $admission->ward->type,
        );
    }

    protected function getConfirmationMessage(): string
    {
        $admission = $this->getAdmission((int) ($this->data['admission_id'] ?? 0));
        $ward = $this->getWard((int) ($this->data['destination_ward_id'] ?? 0));

        if (! $admission || ! $ward) {
            return 'Are you sure you want to transfer this patient?';
        }

        $patientName = $admission->patient?->name ?? 'this patient';
        $currentWard = $admission->ward?->name ?? 'current ward';

        return "{$patientName} will move from {$currentWard} to {$ward->name}. Continue?";
    }

    protected function getAdmission(int $admissionId): ?Admission
    {
        if ($admissionId <= 0) {
            return null;
        }

        if (! array_key_exists($admissionId, $this->admissionCache)) {
            $this->admissionCache[$admissionId] = Admission::query()
                ->with(['patient', 'ward'])
                ->find($admissionId);
        }

        return $this->admissionCache[$admissionId];
    }

    protected function getWard(int $wardId): ?Ward
    {
        if ($wardId <= 0) {
            return null;
        }

        if (! array_key_exists($wardId, $this->wardCache)) {
            $this->wardCache[$wardId] = Ward::query()->find($wardId);
        }

        return $this->wardCache[$wardId];
    }

    protected function getWardOptions(): array
    {
        if ($this->wardOptions !== null) {
            return $this->wardOptions;
        }

        return $this->wardOptions = Ward::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
