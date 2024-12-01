<template x-if="steps.length && !fetchingSteps">
    <template x-for="(step, index) in steps" :key="step.id">
        {{-- Step Row --}}
        <div 
            class="py-1"
            x-bind:class="{
                'border-b border-primary border-dashed': index !== steps.length - 1,
                'text-gray-900': step.is_completed,
                'text-red-500': !step.is_completed,
            }"
        >
            <p class="text-sm whitespace-pre-line" x-html="step.action"></p>
        </div>
    </template>
</template>
