{{-- Steps --}}
@canany(['createSteps', 'updateSteps', 'viewSteps', 'deleteSteps'], App\Models\Document::class)

    <div x-show="showStepsForm" class="flex flex-col p-3 border border-primary border-dashed rounded-lg">

        <h1 class="text-primary font-extrabold mb-3">{{ __('Steps') }}</h1>

        @canany(['updateSteps', 'viewSteps', 'deleteSteps'], App\Models\Document::class)
            <template x-for="step in steps" :key="step.id">
                @include('livewire.pages.documents.modal.steps.row')
            </template>
        @endcanany

        @include('livewire.pages.documents.modal.steps.form')

    </div>

@endcanany
