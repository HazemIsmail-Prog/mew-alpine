<div x-show="showUserForm"
    class=" flex flex-col gap-3 bg-primary px-3 bg-opacity-5 h-full overflow-y-auto w-full lg:w-[416px]  transition-all duration-150 ">
    <div class="p-2 border-b border-primary flex items-center justify-between text-primary font-extrabold ">
        <span>{{ __('New User') }}</span>
        <span>{{ __('Edit User') }}</span>
    </div>

    <div class=" overflow-y-auto flex-1 flex flex-col gap-3">


        {{-- User Form --}}
        <div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
            <h1 class="text-primary font-extrabold">{{ __('User Details') }}</h1>

            {{-- test --}}

            <x-test label="{{ __('Contract') }}" :list="$this->contracts" model="userForm.contract_ids" multiple />
            <x-test label="{{ __('Stakeholder') }}" :list="$this->stakeholders" model="userForm.stakeholder_id" />
            <x-test label="{{ __('Type') }}" :list="$this->types" model="userForm.type" />







            {{-- <x-multi-select-button label="{{ __('Contract') }}" modalType="contract" list="userForm.contract_ids"
                model="contract" /> --}}

            {{-- <x-multi-select-button label="{{ __('Stakeholder') }}" modalType="stakeholder"
                list="userForm.stakeholder_id" model="stakeholder" /> --}}


            <div>
                <x-input-label for="name" :value="__('Name')" />
                <textarea id="name" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                    x-model="userForm.name" rows="3"></textarea>
                <x-input-error :messages="$errors->get('userForm.name')" />
            </div>

            <div>
                <x-input-label for="username" :value="__('Username')" />
                <textarea id="username" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                    x-model="userForm.username" rows="2"></textarea>
                <x-input-error :messages="$errors->get('userForm.username')" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" />
                <textarea id="password" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                    x-model="userForm.password" rows="2"></textarea>
                <x-input-error :messages="$errors->get('userForm.password')" />
            </div>

            <div class=" flex-1"></div>

        </div>

    </div>

    <div class="pt-3 pb-0 border-t border-dashed border-primary">
        <x-primary-button x-on:click="submitUser">{{ __('Save') }}</x-primary-button>
        <x-secondary-button x-on:click="closeUserFormModal()">{{ __('Cancel') }}</x-secondary-button>
    </div>


</div>
