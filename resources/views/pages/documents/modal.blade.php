{{-- Modal --}}
<div x-show="showModal"
    class="flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto w-full lg:w-[416px] transition-all duration-150">
    <div class="p-2 border-b border-primary flex items-center justify-between text-primary font-extrabold">
        <span x-text="form.id ? '{{ __('Edit') }} - '+ form.name : '{{ __('New Record') }}'"></span>
    </div>


    <div class="overflow-y-auto no-scrollbar flex-1 flex flex-col gap-3">
        @include('pages.documents.steps')
        @include('pages.documents.attachments')
        @include('pages.documents.form')
    </div>

    <div class="pt-3 pb-0 border-t border-dashed border-primary">
        <x-primary-button x-on:click="submitRecord">{{ __('Save') }}</x-primary-button>
        <x-secondary-button x-on:click="closeModal">{{ __('Cancel') }}</x-secondary-button>
    </div>
</div>
