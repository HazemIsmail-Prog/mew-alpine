<div x-cloak x-show="stepForm.document_id" x-model="stepForm.document_id" x-modelable="form.id" x-data="stepsSection()"
    class="p-3 border border-primary border-dashed rounded-lg">

    <h1 class="text-primary font-extrabold mb-3">{{ __('Steps') }}</h1>

    @canany(['updateSteps', 'viewSteps', 'deleteSteps'], App\Models\Document::class)

        <template x-if="fetchingSteps">
            <div class=" flex items-center justify-center">
                <x-svg.spinner class=" size-6 text-primary animate-spin" />
            </div>
        </template>

        <template x-if="!steps.length && !fetchingSteps">
            <p class=" text-center text-primary">{{ __('No Steps') }}</p>
        </template>

        <template x-if="form.can_update">
            @include('pages.documents.steps.editable-list')
        </template>

        <template x-if="!form.can_update">
            @include('pages.documents.steps.reading-list')
        </template>
        
    @endcanany

    @can('createSteps', App\Models\Document::class)
        @include('pages.documents.steps.form')
    @endcan

</div>
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/sort@3.x.x/dist/cdn.min.js"></script>

<script>
    function stepsSection() {
        return {
            showSteps: true,
            fetchingSteps: false,
            steps: [],
            stepForm: {
                document_id: null,
                action: '',
                is_completed: false,
            },
            // Define your list of most used words
            mostUsedWords: [
                'المراقب للاعتماد',
                'المراقب للتحويل',
                'م. مصطفى للمراجعة',
                'السكرتارية للحفظ والتحويل',
                'المدير للاعتماد',
                'الوكيل للاعتماد',
                'G2G',
                'استلام المرفقات من الدار',
                'الاستلام من الدار واتساب',
                'الاستلام رسميا',
                'تم تعيين اشارة 00000000000000 بتاريخ',
                new Date().toLocaleDateString('en-GB').replace(/\//g, '-'),
            ],

            handle(id, newPosition) {

                // Make an API call to update the order in the backend
                axios.post(`documents/${this.stepForm.document_id}/steps/reorder`, {
                        id: id,
                        newPosition: newPosition,
                    })
                    .then(response => {
                        this.$dispatch('steps-updated', {
                            documentId: this.stepForm.document_id
                        });;
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
                        this.$dispatch('steps-updated', {
                            documentId: this.stepForm.document_id
                        });
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
                this.fetchingSteps = true;
                axios.get(`documents/${this.stepForm.document_id}/steps`)
                    .then((response) => {
                        this.steps = response.data;
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
                    .finally(() => {
                        this.fetchingSteps = false;
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
                        this.$dispatch('steps-updated', {
                            documentId: this.stepForm.document_id
                        });

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
                        this.$dispatch('steps-updated', {
                            documentId: this.stepForm.document_id
                        });

                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
            },

            toggleStepCompleted(step) {
                axios.put(`documents/${this.stepForm.document_id}/steps/${step.id}/toggle-completed`, step)
                    .then((response) => {
                        step.is_completed = response.data.is_completed
                        this.$dispatch('steps-updated', {
                            documentId: this.stepForm.document_id
                        });

                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

        }
    }
</script>
