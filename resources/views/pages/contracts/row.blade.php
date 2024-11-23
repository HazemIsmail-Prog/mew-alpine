<div class="flex items-center gap-3">
    <div>
        <label x-on:change="toggleRecordIsActive(record, $event.target.checked)"
            class="inline-flex items-center cursor-pointer">
            <input :checked="record.is_active" type="checkbox" class="hidden peer">
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary">
            </div>
        </label>
    </div>

    <div x-on:click="openModal(record)"
        x-bind:class="['flex-1 flex items-center justify-between cursor-pointer border rounded-lg p-2 py-10',
            form.id == record.id ? 'bg-primary !hover:opacity-100 text-white' :
            'bg-zinc-100 hover:bg-primary hover:bg-opacity-25'
        ]">
        <div class="font-extrabold text-lg whitespace-pre-line" x-html="record.name"></div>
    </div>

    <x-svg.delete x-on:click="deleteRecord(record)" class="text-primary size-6" />
</div>
