{{-- Step Row --}}
<div class="flex items-center gap-2 py-1 border-b border-primary border-dashed">

    <div class="flex-1 flex items-center gap-2">
        @can('updateSteps', App\Models\Document::class)
            <input x-model="step.is_completed" x-on:change="toggleStepCompleted(step)" x-bind:checked="step.is_completed"
                type="checkbox" value=""
                class="w-4 h-4 text-primary bg-primary bg-opacity-5 border-primary rounded focus:ring-primary dark:focus:ring-primary dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <template x-if="step.isEditing">
                <textarea class="w-full focus:outline-none focus:ring-0 border-none outline-none" x-trap="step.isEditing"
                    x-on:keydown.ctrl.enter.prevent="confirmEditStep(step)" x-on:keydown.esc.prevent="cancelEditStep(step)"
                    x-model="step.action" rows="3"></textarea>
            </template>
        @endcan
        @can('viewSteps', App\Models\Document::class)
            <template x-if="!step.isEditing">
                <label class="text-sm  dark:text-gray-300 whitespace-pre-line"
                    x-bind:class="step.is_completed ? 'text-gray-900' : 'text-red-500'" x-html="step.action"></label>
            </template>
        @endcan

    </div>

    <div class="flex items-center gap-1">
        @can('updateSteps', App\Models\Document::class)
            <template x-if="!step.isDeleting && !step.isEditing">
                <x-svg.edit x-on:click="editStep(step)" class=" text-primary size-4" />
            </template>
            <template x-if="step.isEditing">
                <div class=" flex items-center gap-2">
                    <x-svg.tick x-show="step.action" x-on:click="confirmEditStep(step)" class=" text-green-500 size-4" />
                    <x-svg.cancel x-on:click="cancelEditStep(step)" class="text-red-500 size-4" />
                </div>
            </template>
        @endcan
        @can('deleteSteps', App\Models\Document::class)
            <template x-if="!step.isDeleting && !step.isEditing">
                <x-svg.delete x-on:click="deleteStep(step)" class="text-red-500 size-4" />
            </template>
            <template x-if="step.isDeleting">
                <div class="flex items-center gap-1">
                    <x-svg.tick x-on:click="confirmDeleteStep(step)" class=" text-green-500 size-4" />
                    <x-svg.cancel x-on:click="cancelDeleteStep(step)" class="text-red-500 size-4" />
                </div>
            </template>
        @endcan
    </div>

</div>