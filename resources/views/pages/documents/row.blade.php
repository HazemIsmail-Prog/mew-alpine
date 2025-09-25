<div class="flex items-center gap-3 w-full lg:w-3/5 " x-bind:class="{ 'self-end': record.type == 'incoming' }">

    {{-- <template x-if="record.can_update">
        <div>
            <label x-on:change="toggleRecordIsCompleted(record, $event.target.checked)"
                class="inline-flex items-center cursor-pointer">
                <input :checked="record.is_completed" type="checkbox" class="hidden peer">
                <div
                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                </div>
            </label>
        </div>
    </template> --}}

    <div x-on:click="openModal(record)"
        x-bind:class="['flex-1 flex flex-col md:flex-row items-center justify-between gap-2 cursor-pointer border rounded-lg p-2',
            form.id == record.id ? 'bg-primary !hover:opacity-100 text-white' :
            'bg-zinc-100 hover:bg-primary hover:bg-opacity-25'
        ]">
        <div class=" w-full">
            <div class=" font-extrabold text-lg whitespace-pre-line" x-html="record.title"></div>
            <!-- <div class="text-sm" x-text="getName('contract', record.contract_id)"></div> -->
            <template x-for="contract_id in record.contract_ids" :key="contract_id">
                <div class="text-sm" x-text="getName('contract', contract_id)"></div>
            </template>
            <div class=" flex items-center gap-1">
                <x-svg.inbox x-show="record.type == 'incoming'" class=" size-4 shrink-0" x-bind:class="form.id == record.id ? 'text-white' : 'text-success'" />
                <x-svg.outbox x-show="record.type == 'outgoing'" class=" size-4 shrink-0" x-bind:class="form.id == record.id ? 'text-white' : 'text-danger'" />
                <div class=" flex flex-col">
                    <div x-show="record.type == 'incoming'" class="text-sm" x-bind:class="form.id == record.id ? 'text-white' : 'text-primary'"
                        x-text="getName('stakeholder',record.from_id)">
                    </div>
                    <div x-show="record.type == 'outgoing'" class="text-sm" x-bind:class="form.id == record.id ? 'text-white' : 'text-primary'"
                        x-text="getName('stakeholder',record.to_id)">
                    </div>
                </div>
            </div>
            <!-- <template x-if="record.follow_ids.length">
                <div class=" mt-1 flex gap-1 items-start">
                    <template x-for="id in record.follow_ids" :key="id">
                        <div class="text-sm rounded-md px-3 bg-primary text-white" x-text="getName('user',id)"></div>
                    </template>
                </div>
            </template> -->
        </div>
        <template x-if="record.uncompleted_steps.length">
            <div class=" w-full md:max-w-[25%] flex flex-col gap-1 items-start md:items-end">
                <template x-for="step in record.uncompleted_steps" :key="step.id">
                    <span
                        class="whitespace-pre-line cursor-pointer bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded"
                        x-html="step.action"></span>
                </template>
            </div>
        </template>
    </div>

    <template x-if="record.can_delete">
        <x-svg.delete x-on:click="deleteRecord(record)" class="text-primary size-6" />
    </template>


</div>
