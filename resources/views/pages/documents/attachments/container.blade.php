{{-- Attachments --}}
@canany(['createAttachments', 'updateAttachments', 'viewAttachments', 'deleteAttachments'], App\Models\Document::class)

    <div x-cloak x-show="attachmentForm.document_id" x-model="attachmentForm.document_id" x-modelable="form.id"
        x-data="attachments()" class="flex flex-col gap-3 p-3 border border-primary border-dashed rounded-lg">

        <h1 class="text-primary font-extrabold">{{ __('Attachments') }}</h1>

        @canany(['updateAttachments', 'viewAttachments', 'deleteAttachments'], App\Models\Document::class)

            <template x-if="fetchingAttachments">
                <div class=" flex items-center justify-center">
                    <x-svg.spinner class=" size-6 text-primary animate-spin" />
                </div>
            </template>

            <template x-if="!attachments.length && !fetchingAttachments">
                <p class=" text-center text-primary">{{ __('No Attachments') }}</p>
            </template>

            <template x-if="form.can_update">
                @include('pages.documents.attachments.editable-list')
            </template>

            <template x-if="!form.can_update">
                @include('pages.documents.attachments.reading-list')
            </template>
            
        @endcanany

        @can('createAttachments', App\Models\Document::class)
            @include('pages.documents.attachments.form')
        @endcan


    </div>

@endcanany


<script>
    function attachments() {
        return {
            showAttachmentsForm: true,
            deletingAttachmentLoader: false,
            creatingAttachmentLoader: false,
            fetchingAttachments: false,
            attachments: [],
            attachmentForm: {
                file: null,
                description: '',
                document_id: null,
            },

            init() {
                this.$watch('attachmentForm.document_id', value => {
                    if (value) {
                        this.resetAttachmentFromExceptDocumentId();
                        this.attachments = [];
                        this.fetchAttachments()
                    }
                });
            },

            resetAttachmentFromExceptDocumentId() {
                this.attachmentForm.file = null; // Explicitly setting file to null
                this.attachmentForm.description = ''; // Ensuring description is an empty string
                if (this.$refs.uploadInput) {
                    this.$refs.uploadInput.value = ''; // Resetting the input field value
                }
            },

            handleFileDrop(event) {
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    this.attachmentForm.file = null;
                    this.attachmentForm.file = files[0];
                    this.attachmentForm.description = files[0].name.replace(/\.[^/.]+$/, "");
                }
            },

            handleFileUpload(event) {
                const files = event.target.files;
                if (files.length > 0) {
                    this.attachmentForm.file = null;
                    this.attachmentForm.file = files[0];
                    this.attachmentForm.description = files[0].name.replace(/\.[^/.]+$/, "");
                }
            },

            fetchAttachments() {
                this.fetchingAttachments = true;
                axios.get(`documents/${this.attachmentForm.document_id}/attachments`)
                    .then((response) => {
                        this.attachments = response.data;
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
                    .finally(() => {
                        this.fetchingAttachments = false;
                    });
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

                this.creatingAttachmentLoader = true;

                const formData = new FormData();
                formData.append('file', this.attachmentForm.file);
                formData.append('description', this.attachmentForm.description);
                formData.append('document_id', this.attachmentForm.document_id);

                axios.post(`documents/${this.attachmentForm.document_id}/attachments`, formData)
                    .then((response) => {
                        this.attachments.push(response.data);
                        this.resetAttachmentFromExceptDocumentId();
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
                    .finally(() => {
                        this.creatingAttachmentLoader = false;
                    })
            },

            editAttachment(attachment) {
                this.attachments.forEach(a => {
                    a.isDeleting = false;
                    a.isEditing = false;
                });
                attachment.isEditing = true;
                attachment.originalDescription = attachment.description;
            },

            cancelEditAttachment(attachment) {
                attachment.description = attachment.originalDescription;
                attachment.isEditing = false;
            },

            confirmEditAttachment(attachment) {

                if (!attachment.description)
                    return

                axios.put(`documents/${this.attachmentForm.document_id}/attachments/${attachment.id} `, attachment)
                    .then((response) => {
                        attachment.isEditing = false;
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
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

            confirmDeleteAttachment(attachment) {
                this.deletingAttachmentLoader = true
                axios.delete(`documents/${this.attachmentForm.document_id}/attachments/${attachment.id} `)
                    .then((response) => {
                        this.attachments = this.attachments.filter(s => s.id !== attachment.id);
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
                    .finally(() => {
                        this.deletingAttachmentLoader = false;
                        this.attachments.forEach(s => {
                            s.isDeleting = false;
                        });
                    })
            },
        }
    }
</script>
