{{-- Modal --}}
<div x-show="showModal"
    class="flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto w-full lg:w-[416px] transition-all duration-150">
    <div class="p-2 border-b border-primary flex items-center justify-between text-primary font-extrabold">
        <span x-text="form.id ? '{{ __('Edit') }} - '+ form.name : '{{ __('New Record') }}'"></span>
    </div>

    {{-- <template x-if="showModal"> --}}
        <div class="overflow-y-auto flex-1 flex flex-col gap-3">
            <div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
                <h1 class="text-primary font-extrabold">{{ __('Record Details') }}</h1>
                <x-dropdown-test id="form.contract_ids" label="{{ __('Contract') }}" :list="$contracts" :multiple="true"
                    model="form.contract_ids" selected-items="form.contract_ids" />

                <x-dropdown-test id="form.stakeholder_id" label="{{ __('Stakeholder') }}" :list="$stakeholders"
                    :multiple="false" model="form.stakeholder_id" selected-items="form.stakeholder_id" />

                <x-dropdown-test id="form.role" label="{{ __('Role') }}" :list="$roles" :multiple="false"
                    model="form.role" selected-items="form.role" />

                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <textarea id="name" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                        x-model="form.name" rows="3"></textarea>
                    {{-- <x-input-error :messages="$errors->get('form.name')" /> --}}
                </div>
                <div>
                    <x-input-label for="username" :value="__('Username')" />
                    <textarea id="username" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                        x-model="form.username" rows="2"></textarea>
                    {{-- <x-input-error :messages="$errors->get('form.username')" /> --}}
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <textarea id="password" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                        x-model="form.password" rows="2"></textarea>
                    {{-- <x-input-error :messages="$errors->get('form.password')" /> --}}
                </div>
            </div>
        </div>
    {{-- </template> --}}

    <div class="pt-3 pb-0 border-t border-dashed border-primary">
        <x-primary-button x-on:click="submitRecord">{{ __('Save') }}</x-primary-button>
        <x-secondary-button x-on:click="closeModal">{{ __('Cancel') }}</x-secondary-button>
    </div>
</div>
