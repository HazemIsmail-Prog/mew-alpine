<div x-data="page()" class="flex h-full">

    {{-- List --}}
    <div class="flex-1 mx-auto overflow-auto no-scrollbar">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            {{-- Create New Buttons --}}
            @can('create', App\Models\Document::class)
                <div class="flex items-center gap-3 my-3">
                    <button x-on:click="newDocument('incoming')" type="button"
                        class="flex items-center justify-center gap-1 h-9 py-1 w-28 rounded-lg text-white font-bold !bg-success px-4">
                        <span aria-hidden="true" class="material-design-icon tray-arrow-down-icon" role="img">
                            <svg fill="currentColor" class="material-design-icon__svg" width="20" height="20"
                                viewBox="0 0 24 24">
                                <path
                                    d="M2 12H4V17H20V12H22V17C22 18.11 21.11 19 20 19H4C2.9 19 2 18.11 2 17V12M12 15L17.55 9.54L16.13 8.13L13 11.25V2H11V11.25L7.88 8.13L6.46 9.55L12 15Z">
                                    <!---->
                                </path>
                            </svg>
                        </span>
                        <span class="hidden md:block">
                            {{ __('Incoming') }}
                        </span>
                    </button>
                    <button x-on:click="newDocument('outgoing')" type="button"
                        class="flex items-center justify-center gap-1 h-9 py-1 w-28 rounded-lg text-white font-bold !bg-danger px-4">
                        <span aria-hidden="true" class="material-design-icon tray-arrow-up-icon" role="img">
                            <svg fill="currentColor" class="material-design-icon__svg" width="20" height="20"
                                viewBox="0 0 24 24">
                                <path
                                    d="M2 12H4V17H20V12H22V17C22 18.11 21.11 19 20 19H4C2.9 19 2 18.11 2 17V12M12 2L6.46 7.46L7.88 8.88L11 5.75V15H13V5.75L16.13 8.88L17.55 7.45L12 2Z">
                                    <!---->
                                </path>
                            </svg>
                        </span>
                        <span class="hidden md:block">
                            {{ __('Outgoing') }}
                        </span>
                    </button>
                </div>
            @endcan

            @include('components.documents.filters')
            <div class="p-2 text-gray-900 flex flex-col gap-2">
                @foreach ($this->documents as $document)
                    @include('components.documents.row')
                @endforeach
            </div>
        </div>
    </div>
    @include('components.documents.container')

</div>

<script>
    function page() {
        return {



            // ################################### Attachments ###############################################

            showAttachmentsForm: true,
            attachments: [],
            attachmentForm: {
                file: null,
                description: '',
                document_id: null,
            },

            closeAttachmentFormModal() {
                this.showAttachmentsForm = false;
                this.attachments = [];
                this.resetAttachmentFromExceptDocumentId();
            },

            resetAttachmentFromExceptDocumentId() {
                this.attachmentForm.file = null;
                this.attachmentForm.description = '';
                this.$refs.uploadInput.value = ''
            },

            handleFileDrop(event) {
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    this.attachmentForm.file = null;
                    this.attachmentForm.file = files[0];
                }


            },

            handleFileUpload(event) {
                const files = event.target.files;
                if (files.length > 0) {
                    this.attachmentForm.file = null;
                    this.attachmentForm.file = files[0];
                }

            },

            async fetchAttachmentsForDocument(documentId) {
                try {
                    const response = await fetch(`/attachments/${documentId}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch attachments');
                    }

                    const attachments = await response.json();
                    // Initialize isEditing and isDeleting as false for each attachment
                    this.attachments = attachments.map(attachment => ({
                        ...attachment,
                        isEditing: false,
                        isDeleting: false
                    }));

                } catch (error) {
                    console.error("Error fetching attachments:", error);
                }
            },

            createNewAttachment() {
                if (!this.attachmentForm.file) {
                    alert('Please upload a file first.');
                    return;
                }
                if (!this.attachmentForm.description.trim()) {
                    alert('Please provide a description for the file.');
                    return;
                }

                const formData = new FormData();
                formData.append('file', this.attachmentForm.file);
                formData.append('description', this.attachmentForm.description);
                formData.append('document_id', this.attachmentForm.document_id);

                fetch('/attachments', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                        },
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                        } else {
                            this.attachments.push(data.attachment);
                            this.resetAttachmentFromExceptDocumentId();
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    });
            },

            editAttachment(attachment) {
                // Cancel edit mode for all other attachments
                this.attachments.forEach(a => {
                    a.isDeleting = false;
                    a.isEditing = false;
                });

                // Enable edit mode for the selected attachment
                attachment.isEditing = true;
                // Backup the original description in case of cancel
                attachment.originalDescription = attachment.description;
            },

            cancelEditAttachment(attachment) {
                // Revert the description text to the original value and exit edit mode
                attachment.description = attachment.originalDescription;
                attachment.isEditing = false;
            },

            async confirmEditAttachment(attachment) {

                if (!attachment.description)
                    return
                try {
                    const response = await fetch(`/attachments/${attachment.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            description: attachment.description
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Failed to save attachment description');
                    }

                    // Exit edit mode
                    attachment.isEditing = false;

                } catch (error) {
                    console.error("Error saving attachment description:", error);
                }
            },

            deleteAttachment(attachment) {
                // Cancel edit mode for all other attachments
                this.attachments.forEach(s => {
                    s.isDeleting = false;
                    s.isEditing = false;
                });

                attachment.isDeleting = true;
            },

            cancelDeleteAttachment(attachment) {
                attachment.isDeleting = false;
            },

            async confirmDeleteAttachment(attachment) {
                try {
                    const response = await fetch(`/attachments/${attachment.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to delete attachment');
                    }

                    // Remove the deleted attachment from the attachments array
                    this.attachments = this.attachments.filter(s => s.id !== attachment.id);

                } catch (error) {
                    console.error("Error deleting attachment:", error);
                }
            },

            // ################################### Attachments ###############################################


            // ################################### Globals ###############################################

            authStakeholderId: @json($this->authStakeholderId),
            contracts: @json($this->contracts),
            stakeholders: @json($this->stakeholders),
            users: @json($this->users),
            tags: @json($this->tags),
            typesList: @json($this->typesList),
            statusesList: @json($this->statusesList),
            filters: @entangle('filters').live,

            init() {
                Livewire.on('documentCreated', document => {
                    this.$nextTick(() => {
                        this.resetDocumentForm()
                        this.resetStepForm()
                        this.resetAttachmentFromExceptDocumentId()
                        this.openDocumentForm(document[0]);
                    });
                });
                Livewire.on('documentDeleted', () => {
                    this.$nextTick(() => {
                        this.closeStepFormModal()
                        this.closeAttachmentFormModal()
                        this.closeDocumentFormModal()
                    });
                });
            },

            getName(type, id) {

                item = null

                switch (type) {
                    case 'type':
                        item = this.typesList.find(e => e.id === id)
                        break;
                    case 'status':
                        item = this.statusesList.find(e => e.id === id)
                        break;
                    case 'user':
                        item = this.users.find(e => e.id === id)
                        break;
                    case 'contract':
                        item = this.contracts.find(e => e.id === id)
                        break;
                    case 'stakeholder':
                        item = this.stakeholders.find(e => e.id === id)
                        break;
                    case 'tag':
                        item = this.tags.find(e => e.id === id)
                        break;
                }

                return item ? item.name : "---";
            },

            get hasFilters() {
                return Object.values(this.filters).some(filter => filter.length > 0);
            },

            resetFilters() {
                Object.keys(this.filters).forEach(key => this.filters[key] = []);
            },


            // ################################### Globals ###############################################



            // ################################### Steps ###############################################

            showStepsForm: false,
            steps: [],
            stepForm: {},
            defaultStepForm: {
                document_id: '',
                action: '',
                is_completed: false,
            },

            // Define your list of most used words
            mostUsedWords: ['المراقب للاعتماد', 'المراقب للتحويل', 'م. مصطفى للمراجعة', 'السكرتارية للحفظ والتحويل',
                'المدير للاعتماد', new Date().toLocaleDateString('en-GB').replace(/\//g, '-')
            ],

            resetStepForm() {
                this.stepForm = {
                    ...this.defaultStepForm
                };
            },

            closeStepFormModal() {
                this.showStepsForm = false;
                this.steps = [];
                this.resetStepForm();
            },

            async createNewStep() {

                if (!this.stepForm.action)
                    return

                try {
                    const response = await fetch('/steps', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify(this.stepForm)
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Server error:', errorText);
                        throw new Error('Failed to save step');
                    }

                    @this.$refresh()

                    const newStep = await response.json();

                    this.steps.push(newStep);

                    this.stepForm.action = '';

                } catch (error) {
                    console.error("Error saving step:", error);
                }
            },

            async toggleStepCompleted(step) {
                try {
                    const response = await fetch(`/steps/${step.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            is_completed: step.is_completed
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Failed to update step completion status');
                    }

                    @this.$refresh()

                } catch (error) {
                    console.error("Error updating step completion status:", error);
                }
            },

            async fetchStepsForDocument(documentId) {
                try {
                    const response = await fetch(`/steps/${documentId}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch steps');
                    }

                    const steps = await response.json();
                    // Initialize isEditing and isDeleting as false for each step
                    this.steps = steps.map(step => ({
                        ...step,
                        isEditing: false,
                        isDeleting: false
                    }));

                } catch (error) {
                    console.error("Error fetching steps:", error);
                }
            },

            editStep(step) {
                // Cancel edit mode for all other steps
                this.steps.forEach(s => {
                    s.isDeleting = false;
                    s.isEditing = false;
                });

                // Enable edit mode for the selected step
                step.isEditing = true;
                // Backup the original action in case of cancel
                step.originalAction = step.action;
            },

            cancelEditStep(step) {
                // Revert the action text to the original value and exit edit mode
                step.action = step.originalAction;
                step.isEditing = false;
            },

            async confirmEditStep(step) {

                if (!step.action)
                    return
                try {
                    const response = await fetch(`/steps/${step.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            action: step.action
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Failed to save step action');
                    }

                    // Exit edit mode
                    step.isEditing = false;
                    @this.$refresh()

                } catch (error) {
                    console.error("Error saving step action:", error);
                }
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

            async confirmDeleteStep(step) {
                try {
                    const response = await fetch(`/steps/${step.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to delete step');
                    }

                    // Remove the deleted step from the steps array
                    this.steps = this.steps.filter(s => s.id !== step.id);
                    @this.$refresh()

                } catch (error) {
                    console.error("Error deleting step:", error);
                }
            },

            appendWord(word) {
                this.stepForm.action += (this.stepForm.action ? ' ' : '') + word;
                this.$refs.newStepActionTextarea.focus();
            },

            // ################################### Steps ###############################################


            // ################################### Document ###############################################

            showDocumentForm: false,
            currentSelectedDocumentId: null,
            documentForm: @entangle('documentForm'),

            resetDocumentForm() {
                this.documentForm.id = null;
                this.documentForm.follow_ids = [];
                this.documentForm.tag_ids = [];
                this.documentForm.contract_id = null;
                this.documentForm.from_id = null;
                this.documentForm.to_id = null;
                this.documentForm.type = null;
                this.documentForm.title = null;
                this.documentForm.content = null;
                this.documentForm.notes = null;
            },

            async openDocumentForm(document = null) {
                if (this.documentForm.id == document.id) {
                    this.closeDocumentFormModal();
                    this.closeStepFormModal();
                    this.closeAttachmentFormModal();
                } else {

                    this.resetStepForm();
                    this.resetDocumentForm();
                    // this.resetAttachmentFromExceptDocumentId();
                    this.showDocumentForm = true;
                    this.showStepsForm = true;
                    this.showAttachmentsForm = true
                    this.documentForm = document
                    this.stepForm.document_id = document.id
                    this.attachmentForm.document_id = document.id
                    await this.fetchAttachmentsForDocument(document.id)
                    await this.fetchStepsForDocument(document.id);
                }
            },

            closeDocumentFormModal() {
                this.showDocumentForm = false;
                this.resetDocumentForm();
            },

            toggleDocumentCompleted(document, newValue) {
                @this.toggleCompleted(document.id, newValue)
            },

            submitDocument() {
                @this.saveDocument();
            },

            newDocument(type) {
                this.closeDocumentFormModal();
                this.closeStepFormModal();
                this.closeAttachmentFormModal();
                this.documentForm.type = type;
                if (type == 'incoming') {
                    this.documentForm.to_id = this.authStakeholderId;
                }
                if (type == 'outgoing') {
                    this.documentForm.from_id = this.authStakeholderId;
                }
                this.showDocumentForm = true;
            },

            // ################################### Document ###############################################
        }
    }
</script>
