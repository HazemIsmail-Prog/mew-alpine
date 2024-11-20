<div wire:key="stakeholder-{{ $stakeholder->id }} - {{ rand() }}" class=" flex items-center gap-3">

    <div>
        <label x-on:change="toggleStakeholderIsActive({{ $stakeholder }},$event.target.checked)"
            class="hidden lg:inline-flex items-center cursor-pointer">
            <input @checked($stakeholder->is_active) type="checkbox" class="hidden peer">
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary">
            </div>
        </label>
    </div>


    <div x-on:click="openStakeholderForm({{ $stakeholder }})"
        x-bind:class="[
            'flex-1 flex items-center justify-between cursor-pointer  border rounded-lg p-2',
            stakeholderForm.id == {{ $stakeholder->id }} ?
            'bg-primary !hover:opacity-100 text-white' :
            'bg-zinc-100 hover:bg-primary hover:bg-opacity-25',
        ]">
        <div>
            <div class=" font-extrabold text-lg whitespace-pre-line">{!! $stakeholder->name !!}
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center gap-3 my-3 text-xs">
        <button wire:confirm="Sure?" wire:click="deleteStakeholder({{ $stakeholder }})" type="button"
            class="flex items-center justify-center gap-1 py-1 rounded-lg text-primary font-bold px-4">
            <span aria-hidden="true" class="material-design-icon tray-arrow-up-icon" role="img">
                <svg fill="currentColor" class="material-design-icon__svg" width="24" height="24"
                    viewBox="0 0 24 24">
                    <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"><!---->
                    </path>
                </svg>
            </span>
        </button>
    </div>

</div>
