<div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
    <h1 class="text-primary font-extrabold">{{ __('Document Details') }}</h1>

    <x-searchable-dropdown id="form.contract_id" label="{{ __('Contract') }}" :list="$contracts" :multiple="false"
        model="form.contract_id" />

    <div x-show="form.type === 'incoming'">
        <x-searchable-dropdown id="form.from_id" label="{{ __('Sender') }}" :list="$stakeholders" :multiple="false"
            model="form.from_id" />
    </div>

    <div x-show="form.type === 'outgoing'">
        <x-searchable-dropdown id="form.to_id" label="{{ __('Receiver') }}" :list="$stakeholders" :multiple="false"
            model="form.to_id" />
    </div>

    <x-searchable-dropdown id="form.follow_ids" label="{{ __('Follwers') }}" :list="$users" :multiple="true"
        model="form.follow_ids" />

    <x-searchable-dropdown id="form.tag_ids" label="{{ __('Tags') }}" :list="$tags" :multiple="true"
        model="form.tag_ids" />


    <div>
        <x-input-label for="title" :value="__('Title')" />
        <textarea id="title" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
            x-model="form.title" rows="3"></textarea>
        {{-- <x-input-error :messages="$errors->get('form.title')" /> --}}
    </div>
    <div>
        <x-input-label for="content" :value="__('Contents')" />
        <textarea id="content" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
            x-model="form.content" rows="2"></textarea>
        {{-- <x-input-error :messages="$errors->get('form.content')" /> --}}
    </div>

    <div>
        <x-input-label for="notes" :value="__('Notes')" />
        <textarea id="notes" class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
            x-model="form.notes" rows="2"></textarea>
        {{-- <x-input-error :messages="$errors->get('form.notes')" /> --}}
    </div>

</div>
