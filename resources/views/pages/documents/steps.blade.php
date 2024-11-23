<div x-cloak x-show="stepForm.document_id" x-model="stepForm.document_id" x-modelable="form.id" x-data="stepsSection()"
    class="p-3 border border-primary border-dashed rounded-lg">
    <h1 class="text-primary font-extrabold mb-3">{{ __('Steps') }}</h1>

    @canany(['updateSteps', 'viewSteps', 'deleteSteps'], App\Models\Document::class)
        <div x-sort.ghost="handle">


            <template x-for="step in steps" :key="step.id">
                {{-- Step Row --}}
                <div x-sort:item="step.id" class="flex items-center gap-2 py-1 border-b border-primary border-dashed">
                    <div class="flex-1 flex items-center gap-2">
                        @can('updateSteps', App\Models\Document::class)
                            <input x-model="step.is_completed" x-on:change="toggleStepCompleted(step)"
                                x-bind:checked="step.is_completed" type="checkbox" value=""
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
                                    x-bind:class="step.is_completed ? 'text-gray-900' : 'text-red-500'"
                                    x-html="step.action"></label>
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
                                    <x-svg.tick x-show="step.action" x-on:click="confirmEditStep(step)"
                                        class=" text-green-500 size-4" />
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
            </template>
        </div>
    @endcanany

    {{-- New Step Form Section --}}
    @can('createSteps', App\Models\Document::class)
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
                        class=" cursor-pointer bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300"
                        x-text="word"></span>
                </template>
            </div>
        </div>
    @endcan

</div>
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/sort@3.x.x/dist/cdn.min.js"></script>

<script>
    function stepsSection() {
        return {
            showSteps: true,
            steps: [],
            stepForm: {
                document_id: null,
                action: '',
                is_completed: false,
            },
            // Define your list of most used words
            mostUsedWords: ['المراقب للاعتماد', 'المراقب للتحويل', 'م. مصطفى للمراجعة', 'السكرتارية للحفظ والتحويل',
                'المدير للاعتماد', new Date().toLocaleDateString('en-GB').replace(/\//g, '-')
            ],

            handle(id, newPosition) {

                // Make an API call to update the order in the backend
                axios.post(`documents/${this.stepForm.document_id}/steps/reorder`, {
                        id: id,
                        newPosition: newPosition,
                    })
                    .then(response => {
                        this.$dispatch('steps-updated');
                    })
                    .catch(error => {
                        console.error("Error updating order:", error.response.data.message);
                        alert(error.response.data.message);
                    });
            },

            updateStepOrder(orderedIds) {
                // Prepare data to send to the server
                const data = {
                    ordered_ids: orderedIds
                };

                console.log(data);
            },

            appendWord(word) {
                this.stepForm.action += (this.stepForm.action ? ' ' : '') + word;
                this.$refs.newStepActionTextarea.focus();
            },

            createStep() {
                if (!this.stepForm.action)
                    return

                axios.post(`documents/${this.stepForm.document_id}/steps`, this.stepForm)
                    .then((response) => {
                        this.stepForm.action = '';
                        this.steps.push(response.data)
                        this.$dispatch('steps-updated')
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            init() {
                this.$watch('stepForm.document_id', value => {
                    if (value) {
                        this.stepForm.action = '';
                        this.steps = [];
                        this.fetchSteps()
                    }
                });
                this.handle = this.handle.bind(this);

            },

            fetchSteps() {
                axios.get(`documents/${this.stepForm.document_id}/steps`)
                    .then((response) => {
                        this.steps = response.data;
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            editStep(step) {
                this.steps.forEach(s => {
                    s.isDeleting = false;
                    s.isEditing = false;
                });

                step.isEditing = true;
                step.originalAction = step.action;
            },

            cancelEditStep(step) {
                step.action = step.originalAction;
                step.isEditing = false;
            },

            confirmEditStep(step) {
                if (!step.action)
                    return

                axios.put(`documents/${this.stepForm.document_id}/steps/${step.id}`, step)
                    .then((response) => {
                        step.action = response.data.action
                        step.isEditing = false;
                        this.$dispatch('steps-updated')
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            deleteStep(step) {
                // Cancel edit mode for all other steps
                this.steps.forEach(s => {
                    s.isDeleting = false;
                    s.isEditing = false;
                });

                step.isDeleting = true;
            },

            cancelDeleteStep(step) {
                step.isDeleting = false;
            },

            confirmDeleteStep(step) {
                axios.delete(`documents/${this.stepForm.document_id}/steps/${step.id}`)
                    .then((response) => {
                        this.steps = this.steps.filter(s => s.id !== step.id);
                        this.$dispatch('steps-updated')
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
            },

            toggleStepCompleted(step) {
                axios.put(`documents/${this.stepForm.document_id}/steps/${step.id}/toggle-completed`, step)
                    .then((response) => {
                        step.is_completed = response.data.is_completed
                        this.$dispatch('steps-updated')
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

        }
    }
</script>
