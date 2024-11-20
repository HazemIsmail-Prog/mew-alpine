{{-- Attachments --}}
@canany(['createAttachments', 'updateAttachments', 'viewAttachments', 'deleteAttachments'], App\Models\Document::class)

    <div x-show="showAttachmentsForm" class="flex flex-col gap-3 p-3 border border-primary border-dashed rounded-lg">

        <h1 class="text-primary font-extrabold">{{ __('Attachments') }}</h1>

        @canany(['updateAttachments', 'viewAttachments', 'deleteAttachments'], App\Models\Document::class)
            <template x-if="attachments.length">
                <div>
                    <template x-for="attachment in attachments" :key="attachment.id">
                        @include('livewire.pages.documents.modal.attachments.row')
                    </template>
                </div>
            </template>
        @endcanany

        @include('livewire.pages.documents.modal.attachments.form')

    </div>

@endcanany
