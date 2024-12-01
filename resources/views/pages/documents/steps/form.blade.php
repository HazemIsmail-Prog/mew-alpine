{{-- New Step Form Section --}}
    <div class="flex gap-2 flex-col mt-2">
        <div class="flex items-center gap-2">
            <textarea rows="2" class="w-full focus:outline-none focus:ring-0 border-none outline-none"
                x-ref="newStepActionTextarea" x-model="stepForm.action" x-on:keydown.ctrl.enter.prevent="createStep"></textarea>
            <x-svg.save class="text-primary size-4" x-show="stepForm.action" x-on:click="createStep" />
            <x-svg.cancel class="text-red-500 size-4" x-show="stepForm.action" x-on:click="stepForm.action = ''" />
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <template x-for="word in mostUsedWords" :key="word">
                <span x-on:click="appendWord(word)"
                    class=" cursor-pointer bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded"
                    x-text="word"></span>
            </template>
        </div>
    </div>
