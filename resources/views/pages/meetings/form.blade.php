<div x-show="showModal" x-cloak
    class="flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto w-full lg:w-[416px] transition-all duration-150">
    <div class="p-2 border-b border-primary flex items-center justify-between text-primary font-extrabold">
        <span class="text-sm sm:text-base" x-text="form.id ? '{{ __('Edit Meeting') }}' : '{{ __('New Meeting') }}'"></span>
        <button x-on:click="closeModal()" type="button" class="lg:hidden p-1 rounded hover:bg-gray-200 active:bg-gray-300 touch-manipulation">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <div class="overflow-y-auto flex-1 flex flex-col gap-3">
        <div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
            <h1 class="text-primary font-extrabold">{{ __('Meeting Details') }}</h1>

            <div>
                <x-input-label for="title" :value="__('Title')" />
                <x-text-input type="text" class="w-full" id="title" x-model="form.title" />
            </div>

            <div>
                <x-input-label for="date" :value="__('Meeting Date')" />
                <x-text-input type="date" class="w-full" id="date" x-model="form.date" />
            </div>

            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <x-input-label for="start_time" :value="__('Start Time')" />
                    <x-text-input type="time" class="w-full" id="start_time" x-model="form.start_time" />
                </div>
                <div class="flex-1">
                    <x-input-label for="end_time" :value="__('End Time')" />
                    <x-text-input type="time" class="w-full" id="end_time" x-model="form.end_time" />
                </div>
            </div>

            <div>
                <x-input-label for="location" :value="__('Meeting Location')" />
                <x-text-input list="locations" type="text" class="w-full" id="location" x-model="form.location" />
                <template x-if="locations.length > 0">
                    <datalist id="locations">
                        <template x-for="location in locations" :key="location">
                            <option :value="location" x-text="location"></option>
                        </template>
                    </datalist>
                </template>
            </div>



            <div>
                <x-input-label for="attendees" :value="__('Attendees')" />
                <textarea id="attendees" class="w-full rounded-md focus:outline-none focus:ring-0 border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                    x-model="form.attendees" rows="4"></textarea>
            </div>

            <x-dropdown-test id="form.contract_ids" label="{{ __('Contract') }}" :list="$contracts" :multiple="true"
            model="form.contract_ids" selected-items="form.contract_ids" />

            <div>
                <x-input-label for="notes" :value="__('Notes')" />
                <textarea id="notes" class="w-full rounded-md focus:outline-none focus:ring-0 border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                    x-model="form.notes" rows="3"></textarea>
            </div>

            <div>
                <x-input-label for="agenda" :value="__('Agenda')" />
                <textarea id="agenda" class="w-full rounded-md focus:outline-none focus:ring-0 border-gray-300 shadow-sm focus:border-primary focus:ring-primary"
                    x-model="form.agenda" rows="3"></textarea>
            </div>
        </div>
    </div>

    <div class="pt-3 pb-0 border-t border-dashed border-primary">
        <x-primary-button x-on:click="submitRecord">{{ __('Save') }}</x-primary-button>
        <x-secondary-button x-on:click="closeModal">{{ __('Cancel') }}</x-secondary-button>
    </div>
</div>
