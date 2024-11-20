<div x-cloak x-show="showDocumentForm"
    class=" flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto no-scrollbar w-full lg:w-[416px]  transition-all duration-150 ">
    <div class="p-2 border-b border-primary flex items-center justify-between text-primary font-extrabold ">
        <span>{{ __('New Document') }}</span>
        <span>{{ __('Edit Document') }}</span>
    </div>

    <div class=" overflow-y-auto no-scrollbar flex-1 flex flex-col gap-3">

        @include('components.steps.container')

        @include('components.attachments.container')

        @include('components.documents.form')

    </div>

    <div class="pt-3 pb-0 border-t border-dashed border-primary">
        <x-primary-button x-on:click="submitDocument">{{ __('Save') }}</x-primary-button>
        <x-secondary-button
            x-on:click="closeDocumentFormModal();closeStepFormModal()">{{ __('Cancel') }}</x-secondary-button>
    </div>


</div>
