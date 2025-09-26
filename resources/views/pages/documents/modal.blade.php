{{-- Modal --}}
<div x-show="showModal"
    class="print:hidden flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto w-full lg:w-[416px] transition-all duration-150">
    <div class="p-2 border-b border-primary flex items-center gap-3 text-primary font-extrabold">
        <button x-on:click="closeModal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>

        </button>
        <span x-text="form.id ? '{{ __('Edit Document') }}' : '{{ __('New Document') }}'"></span>
    </div>


    <div class="overflow-y-auto no-scrollbar flex-1 flex flex-col gap-3">
        @include('pages.documents.steps.container')
        @include('pages.documents.attachments.container')

        <template x-if="form.can_update">
            @include('pages.documents.form')
        </template>

        <template x-if="!form.can_update">
            @include('pages.documents.reading-details')
        </template>
    </div>

    <div id="saveFromButtons"></div>

</div>
