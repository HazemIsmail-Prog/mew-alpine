<div class="flex flex-col p-3 gap-3 border border-primary border-dashed rounded-lg">
    <h1 class="text-primary font-extrabold">{{ __('Document Details') }}</h1>
    <div>
        <div class="mb-3">
            <x-input-label for="contract_id" :value="__('Contract')" />
            <p x-html="getName('contract',form.contract_id)"></p>
        </div>

        <div class="mb-3" x-show="form.type === 'incoming'">
            <x-input-label for="from_id" :value="__('Sender')" />
            <p x-html="getName('stakeholder',form.from_id)"></p>
        </div>

        <div class="mb-3" x-show="form.type === 'outgoing'">
            <x-input-label for="to_id" :value="__('Receiver')" />
            <p x-html="getName('stakeholder',form.to_id)"></p>
        </div>

        <div class="mb-3">
            <x-input-label for="title" :value="__('Title')" />
            <p class=" whitespace-pre-line" x-html="form.title"></p>
        </div>

        <div class="mb-3" x-show="form.content">
            <x-input-label for="content" :value="__('Contents')" />
            <p class=" whitespace-pre-line" x-html="form.content"></p>
        </div>

        <div class="mb-3" x-show="form.notes">
            <x-input-label for="notes" :value="__('Notes')" />
            <p class=" whitespace-pre-line" x-html="form.notes"></p>
        </div>
    </div>
</div>
