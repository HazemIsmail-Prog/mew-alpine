<div x-show="showModal" x-cloak
    class="flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto w-full lg:w-[416px] transition-all duration-150">
    <div class="p-2 border-b border-primary flex items-center justify-between text-primary font-extrabold">
        <span class="text-sm sm:text-base" x-text="form.id ? '{{ __('Edit') }} - '+ form.name : '{{ __('New Record') }}'"></span>
        <button x-on:click="closeModal()" type="button" class="lg:hidden p-1 rounded hover:bg-gray-200 active:bg-gray-300 touch-manipulation">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="overflow-y-auto flex-1 flex flex-col gap-3">
        <div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
            <h1 class="text-primary font-extrabold">{{ __('Record Details') }}</h1>


            <x-dropdown-test id="form.user_id" label="{{ __('User') }}" :list="$users"
                :multiple="false" model="form.user_id" selected-items="form.user_id" />


            <div>
                <x-input-label for="start_date" :value="__('Start Date')" />
                <x-text-input type="date" class="w-full" id="start_date" x-model="form.start_date" />
            </div>
            <div>
                <x-input-label for="end_date" :value="__('End Date')" />
                <x-text-input type="date" class="w-full" id="end_date" x-model="form.end_date" />
            </div>
        </div>
    </div>

    <div class="pt-3 pb-0 border-t border-dashed border-primary">
        <x-primary-button x-on:click="submitRecord">{{ __('Save') }}</x-primary-button>
        <x-secondary-button x-on:click="closeModal">{{ __('Cancel') }}</x-secondary-button>
    </div>
</div>
