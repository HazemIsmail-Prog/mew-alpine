<div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
    <h1 class="text-primary font-extrabold">{{ __('Document Details') }}</h1>
    <div>

        <x-dropdown-test id="form.contract_id" label="{{ __('Contract') }}" :list="$contracts" :multiple="false"
            model="form.contract_id" selected-items="form.contract_id" />

        <div x-show="form.type === 'incoming'">
            <x-dropdown-test id="form.from_id" label="{{ __('Sender') }}" :list="$stakeholders" :multiple="false"
            model="form.from_id" selected-items="form.from_id" />
        </div>

        <div x-show="form.type === 'outgoing'">
            <x-dropdown-test id="form.to_id" label="{{ __('Receiver') }}" :list="$stakeholders" :multiple="false"
            model="form.to_id" selected-items="form.to_id" />
        </div>

        <x-dropdown-test id="form.follow_ids" label="{{ __('Follwers') }}" :list="$users" :multiple="true"
        model="form.follow_ids" selected-items="form.follow_ids" />

        <x-dropdown-test id="form.tag_ids" label="{{ __('Tags') }}" :list="$tags" :multiple="true"
        model="form.tag_ids" selected-items="form.tag_ids" />

        <div>
            <x-input-label for="title" :value="__('Title')" />
            <textarea id="title" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                x-model="form.title" rows="3"></textarea>
        </div>
        <div>
            <x-input-label for="content" :value="__('Contents')" />
            <textarea id="content" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                x-model="form.content" rows="2"></textarea>
        </div>

        <div>
            <x-input-label for="notes" :value="__('Notes')" />
            <textarea id="notes" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                x-model="form.notes" rows="2"></textarea>
        </div>
    </div>


    <template x-teleport="#saveFromButtons">
        <div class="pt-3 pb-0 border-t border-dashed border-primary">
            <x-primary-button x-on:click="submitRecord">{{ __('Save') }}</x-primary-button>
            <x-secondary-button x-on:click="closeModal">{{ __('Cancel') }}</x-secondary-button>
        </div>
    </template>

</div>
