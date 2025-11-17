<?php

namespace App\Filament\Pages;

use App\Models\Admission;
use BackedEnum;
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
use UnitEnum;

class DischargePatient extends Page
{
    protected static ?string $title = 'Discharge Patient';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'discharge';

    protected static string|UnitEnum|null $navigationGroup = 'Care Actions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-minus';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('patient.discharge') ?? false;
    }

    public ?array $data = [];

    /**
     * @var array<int, Admission|null>
     */
    protected array $admissionCache = [];

    protected ?array $preloadedAdmissionOptions = null;

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
                Section::make('Discharge details')
                    ->columns(2)
                    ->schema([
                        Select::make('admission_id')
                            ->label('Patient')
                            ->options(fn (): array => $this->getPreloadedAdmissionOptions())
                            ->preload()
                            ->placeholder('Search admitted patients...')
                            ->searchable()
                            ->live()
                            ->getSearchResultsUsing(fn (string $search): array => $this->searchAdmissions($search))
                            ->getOptionLabelUsing(fn ($value): ?string => $this->getAdmissionOptionLabel($value))
                            ->required(),
                        Placeholder::make('current_ward')
                            ->label('Current ward:')
                            ->content(fn (callable $get): string => $this->getCurrentWardDisplay($get('admission_id')))
                            ->columnSpan(2),
                        Placeholder::make('admitted_at')
                            ->label('Admitted on:')
                            ->content(fn (callable $get): string => $this->getAdmittedAtDisplay($get('admission_id')))
                            ->columnSpan(2),
                    ])->columnSpanFull(),
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
            ->id('discharge-form')
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
            Action::make('discharge')
                ->label('Discharge patient')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->discharge())
                ->modalHeading('Confirm discharge')
                ->modalDescription(fn (): string => $this->getConfirmationMessage())
                ->disabled(fn (): bool => blank($this->data['admission_id'] ?? null)),
        ];
    }

    public function discharge(): void
    {
        $this->callHook('beforeValidate');
        $data = $this->form->getState();
        $this->callHook('afterValidate');

        $admission = $this->getAdmission((int) ($data['admission_id'] ?? 0));

        if (! $admission) {
            Notification::make()
                ->title('Select a patient')
                ->body('Choose an admitted patient to discharge.')
                ->danger()
                ->send();

            return;
        }

        $patient = $admission->patient;
        $patientName = $patient?->name ?? 'The patient';

        if ($patient) {
            $patient->delete();
        } else {
            $admission->delete();
        }

        Notification::make()
            ->success()
            ->title('Patient discharged')
            ->body("{$patientName} has been discharged.")
            ->send();

        $this->admissionCache = [];
        $this->preloadedAdmissionOptions = null;
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
            return 'Select a patient to view their ward.';
        }

        return sprintf(
            '%s (%s)',
            $admission->ward->name,
            $admission->ward->type,
        );
    }

    protected function getAdmittedAtDisplay(mixed $admissionId): string
    {
        $admission = $this->getAdmission((int) $admissionId);

        if (! $admission?->admitted_at) {
            return 'Select a patient to view admission time.';
        }

        return $admission->admitted_at->format('M d, Y g:i A');
    }

    protected function getConfirmationMessage(): string
    {
        $admission = $this->getAdmission((int) ($this->data['admission_id'] ?? 0));

        if (! $admission) {
            return 'Are you sure you want to discharge this patient?';
        }

        $patientName = $admission->patient?->name ?? 'this patient';
        $ward = $admission->ward?->name ?? 'their ward';

        return "{$patientName} will be discharged from {$ward}. Continue?";
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

    protected function getPreloadedAdmissionOptions(): array
    {
        if ($this->preloadedAdmissionOptions !== null) {
            return $this->preloadedAdmissionOptions;
        }

        return $this->preloadedAdmissionOptions = $this->searchAdmissions('');
    }
}
