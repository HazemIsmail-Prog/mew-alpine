<template x-if="steps.length && !fetchingSteps">
    <template x-for="step in steps" :key="step.id">
        {{-- Step Row --}}
        <div class="py-1 border-b border-primary border-dashed">
            <label class="text-sm whitespace-pre-line"
                x-bind:class="step.is_completed ? 'text-gray-900' : 'text-red-500'" x-html="step.action"></label>
        </div>
    </template>
</template>
