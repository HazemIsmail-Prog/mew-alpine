{{-- Modal --}}
<div x-show="showModal"
    class="flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto w-full lg:w-[416px] transition-all duration-150">
    <div class="p-2 border-b border-primary flex items-center justify-between text-primary font-extrabold">
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
