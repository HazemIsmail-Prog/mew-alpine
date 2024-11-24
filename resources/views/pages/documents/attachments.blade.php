{{-- Attachments --}}
@canany(['createAttachments', 'updateAttachments', 'viewAttachments', 'deleteAttachments'], App\Models\Document::class)

    <div x-cloak x-show="attachmentForm.document_id" x-model="attachmentForm.document_id" x-modelable="form.id"
        x-data="attachments()" class="flex flex-col gap-3 p-3 border border-primary border-dashed rounded-lg">

        <h1 class="text-primary font-extrabold">{{ __('Attachments') }}</h1>

        @canany(['updateAttachments', 'viewAttachments', 'deleteAttachments'], App\Models\Document::class)
            <template x-if="attachments.length">
                <div>
                    <template x-for="attachment in attachments" :key="attachment.id">
                        {{-- Attachment Row --}}
                        <div>
                            <div x-show="!attachment.isDeleting || !deletingAttachmentLoader"
                                class="flex items-center gap-2 py-1 border-b border-primary border-dashed">
                                <div class="flex-1 flex items-center gap-2">
                                    @can('viewAttachments', App\Models\Document::class)
                                        <a x-show="!attachment.isEditing"
                                            class="text-sm text-gray-900 dark:text-gray-300 whitespace-pre-line"
                                            x-html="attachment.description" target="__blank" x-bind:href="attachment.file_path"></a>
                                    @endcan
                                    @can('updateAttachments', App\Models\Document::class)
                                        <x-text-input x-show="attachment.isEditing" x-trap="attachment.isEditing"
                                            x-on:keydown.ctrl.enter.prevent="confirmEditAttachment(attachment)"
                                            x-on:keydown.esc.prevent="cancelEditAttachment(attachment)"
                                            x-model="attachment.description"
                                            class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                                            type="text" />
                                    @endcan
                                </div>
                                @canany(['updateAttachments', 'deleteAttachments'], App\Models\Document::class)
                                    <div class="flex items-center gap-1">
                                        @can('updateAttachments', App\Models\Document::class)
                                            <x-svg.edit x-show="!attachment.isDeleting && !attachment.isEditing"
                                                x-on:click="editAttachment(attachment)" class="text-primary size-4" />
                                            <div x-show="attachment.isEditing" class=" flex items-center gap-2">
                                                <x-svg.tick x-show="attachment.description"
                                                    x-on:click="confirmEditAttachment(attachment)" class="text-success size-4" />
                                                <x-svg.cancel x-on:click="cancelEditAttachment(attachment)"
                                                    class="text-danger size-4" />
                                            </div>
                                        @endcan
                                        @can('deleteAttachments', App\Models\Document::class)
                                            <x-svg.delete x-show="!attachment.isDeleting && !attachment.isEditing"
                                                x-on:click="deleteAttachment(attachment)" class=" text-danger size-4" />
                                            <div x-show="attachment.isDeleting" class="flex items-center gap-1">
                                                <x-svg.tick x-show="attachment.description"
                                                    x-on:click="confirmDeleteAttachment(attachment)" class="text-success size-4" />
                                                <x-svg.cancel x-on:click="cancelDeleteAttachment(attachment)"
                                                    class="text-danger size-4" />
                                            </div>
                                        @endcan
                                    </div>
                                @endcanany
                            </div>
                            <div x-show="attachment.isDeleting && deletingAttachmentLoader"
                                class="flex items-center gap-2 py-1 border-b border-primary border-dashed">
                                <span class=" text-primary animate-pulse">{{ __('Deleting...') }}</span>
                            </div>
                        </div>

                    </template>
                </div>
            </template>
        @endcanany

        @can('createAttachments', App\Models\Document::class)
            <div class="flex items-center justify-center w-full" x-show="!attachmentForm.file" x-on:dragover.prevent
                x-on:dragleave.prevent x-on:drop.prevent="handleFileDrop($event)">

                <label for="dropzone-file"
                    class="flex flex-col items-center justify-center w-full h-14 border border-primary border-dashed rounded-lg cursor-pointer">

                    <p class="text-sm text-primary select-none">
                        <span class="font-semibold">
                            {{ __('Click to upload') }}
                        </span>
                        {{ __('or drag and drop') }}
                    </p>

                    <input id="dropzone-file" type="file" class="hidden" x-ref="uploadInput"
                        x-on:change="handleFileUpload($event)" />

                </label>

            </div>

            <!-- Description Input and Save/Cancel Buttons -->
            <div x-show="attachmentForm.file && !creatingAttachmentLoader" class="flex items-center gap-2">
                <x-text-input placeholder="Description"
                    class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none" type="text"
                    x-model="attachmentForm.description" x-trap="attachmentForm.file"
                    x-on:keydown.ctrl.enter.prevent="createNewAttachment" />

                <div class="flex items-center gap-2">
                    <x-svg.save class="text-primary size-4" x-show="attachmentForm.description"
                        x-on:click="createNewAttachment" />

                    <x-svg.cancel class="text-red-500 size-4" x-on:click="resetAttachmentFromExceptDocumentId" />
                </div>
            </div>

            <!-- Uploading Indicator -->
            <p x-show="creatingAttachmentLoader"
                class="text-primary text-center animate-pulse border border-primary border-dashed rounded-lg p-3">
                {{ __('Uploading...') }}</p>
        @endcan


    </div>

@endcanany


<script>
    function attachments() {
        return {
            showAttachmentsForm: true,
            deletingAttachmentLoader: false,
            creatingAttachmentLoader: false,
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

            fetchAttachments() {
                axios.get(`documents/${this.attachmentForm.document_id}/attachments`)
                    .then((response) => {
                        this.attachments = response.data;
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
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
