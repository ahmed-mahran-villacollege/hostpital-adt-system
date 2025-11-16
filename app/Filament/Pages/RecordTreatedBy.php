<?php

namespace App\Filament\Pages;

use App\Models\Admission;
use App\Models\Doctor;
use App\Models\TreatedBy;
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

class RecordTreatedBy extends Page
{
    protected static ?string $title = 'Record Treated By';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'treated-by';

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
                Section::make('Record treated by')
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
                        Select::make('doctor_id')
                            ->label('Doctor')
                            ->placeholder('Select treating doctor')
                            ->options(fn (callable $get): array => $this->getDoctorOptions((int) $get('admission_id')))
                            ->searchable()
                            ->live()
                            ->preload()
                            ->required()
                            ->disabled(fn (callable $get): bool => blank($get('admission_id')))
                            ->helperText("Only doctors from the assigned team are listed."),
                        Placeholder::make('team_info')
                            ->label('Assigned team:')
                            ->content(fn (callable $get): string => $this->getTeamDisplay($get('admission_id'))),
                        Placeholder::make('admission_summary')
                            ->label('Current ward:')
                            ->content(fn (callable $get): string => $this->getWardDisplay($get('admission_id')))
                            ->columnSpan(2),
                    ])->columns(2)->columnSpanFull(),
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
            ->id('treated-by-form')
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
            Action::make('record')
                ->label('Create record')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn () => $this->recordTreatment())
                ->modalHeading('Confirm treatment record')
                ->modalDescription(fn (): string => $this->getConfirmationMessage())
                ->disabled(fn (): bool => blank($this->data['admission_id'] ?? null) || blank($this->data['doctor_id'] ?? null)),
        ];
    }

    public function recordTreatment(): void
    {
        $this->callHook('beforeValidate');
        $data = $this->form->getState();
        $this->callHook('afterValidate');

        $admission = $this->getAdmission((int) ($data['admission_id'] ?? 0));
        $doctorId = (int) ($data['doctor_id'] ?? 0);

        if (! $admission || $doctorId <= 0) {
            Notification::make()
                ->title('Select patient and doctor')
                ->body('Choose a patient and a doctor to continue.')
                ->danger()
                ->send();

            return;
        }

        if (! $this->doctorBelongsToAdmissionTeam($admission, $doctorId)) {
            Notification::make()
                ->title('Doctor not on team')
                ->body("Only doctors from the assigned team can be recorded.")
                ->danger()
                ->send();

            return;
        }

        TreatedBy::query()->create([
            'admission_id' => $admission->getKey(),
            'doctor_id' => $doctorId,
            'treated_at' => now(),
        ]);

        $patientName = $admission->patient?->name ?? 'The patient';
        $doctorName = $this->getDoctorName($doctorId);

        Notification::make()
            ->success()
            ->title('Treatment recorded')
            ->body("{$doctorName} has been recorded as treating {$patientName}.")
            ->send();

        $this->form->fill();
    }

    /**
     * @return array<int, string>
     */
    protected function searchAdmissions(string $search): array
    {
        return Admission::query()
            ->with(['patient', 'ward', 'team'])
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

    protected function getTeamDisplay(mixed $admissionId): string
    {
        $admission = $this->getAdmission((int) $admissionId);

        if (! $admission?->team) {
            return 'Select a patient to view their team.';
        }

        $team = $admission->team;

        return sprintf(
            '%s (%s)',
            $team->name,
            $team->code ?? 'No code',
        );
    }

    protected function getWardDisplay(mixed $admissionId): string
    {
        $admission = $this->getAdmission((int) $admissionId);

        if (! $admission?->ward) {
            return 'Select a patient to view ward details.';
        }

        return sprintf(
            '%s (%s)',
            $admission->ward->name,
            $admission->ward->type,
        );
    }

    protected function getDoctorOptions(int $admissionId): array
    {
        $admission = $this->getAdmission($admissionId);

        if (! $admission?->team) {
            return [];
        }

        $options = $admission->team
            ->doctors()
            ->select(['doctors.id', 'doctors.name'])
            ->orderBy('doctors.name')
            ->pluck('doctors.name', 'doctors.id');

        $consultant = $admission->team->consultant;

        if ($consultant && ! $options->has($consultant->getKey())) {
            $options->put($consultant->getKey(), $consultant->name);
        }

        return $options
            ->sort()
            ->all();
    }

    protected function getConfirmationMessage(): string
    {
        $admission = $this->getAdmission((int) ($this->data['admission_id'] ?? 0));
        $doctorId = (int) ($this->data['doctor_id'] ?? 0);

        if (! $admission || $doctorId <= 0) {
            return 'Are you sure you want to record this treatment?';
        }

        $patientName = $admission->patient?->name ?? 'this patient';
        $doctorName = $this->getDoctorName($doctorId) ?? 'the selected doctor';

        return "{$doctorName} will be recorded as having treated {$patientName}. Continue?";
    }

    protected function getDoctorName(int $doctorId): ?string
    {
        return Doctor::query()
            ->whereKey($doctorId)
            ->value('name');
    }

    protected function doctorBelongsToAdmissionTeam(Admission $admission, int $doctorId): bool
    {
        if (! $admission->team) {
            return false;
        }

        return $admission->team
            ->doctors()
            ->whereKey($doctorId)
            ->exists();
    }

    protected function getAdmission(int $admissionId): ?Admission
    {
        if ($admissionId <= 0) {
            return null;
        }

        if (! array_key_exists($admissionId, $this->admissionCache)) {
            $this->admissionCache[$admissionId] = Admission::query()
                ->with(['patient', 'ward', 'team'])
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
