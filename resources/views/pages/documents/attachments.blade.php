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
                        <div class="flex items-center gap-2 py-1 border-b border-primary border-dashed">

                            <div class="flex-1 flex items-center gap-2">
                                @can('viewAttachments', App\Models\Document::class)
                                    <template x-if="!attachment.isEditing">
                                        <a class="text-sm text-gray-900 dark:text-gray-300 whitespace-pre-line"
                                            x-html="attachment.description" target="__blank" x-bind:href="attachment.file"></a>

                                    </template>
                                @endcan
                                @can('updateAttachments', App\Models\Document::class)
                                    <template x-if="attachment.isEditing">
                                        <x-text-input x-trap="attachment.isEditing"
                                            x-on:keydown.ctrl.enter.prevent="confirmEditAttachment(attachment)"
                                            x-on:keydown.esc.prevent="cancelEditAttachment(attachment)"
                                            x-model="attachment.description"
                                            class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none"
                                            type="text" />
                                    </template>
                                @endcan

                            </div>


                            @canany(['updateAttachments', 'deleteAttachments'], App\Models\Document::class)
                                <div class="flex items-center gap-1">

                                    @can('updateAttachments', App\Models\Document::class)
                                        <template x-if="!attachment.isDeleting && !attachment.isEditing">
                                            <svg x-on:click="editAttachment(attachment)" fill="currentColor"
                                                class="text-primary material-design-icon__svg" width="18" height="18"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z">
                                                </path>
                                            </svg>
                                        </template>
                                        <template x-if="attachment.isEditing">
                                            <div class=" flex items-center gap-2">
                                                <svg x-show="attachment.description" x-on:click="confirmEditAttachment(attachment)"
                                                    fill="currentColor" class="text-green-500 material-design-icon__svg" width="18"
                                                    height="18" viewBox="0 0 24 24">
                                                    <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path>
                                                </svg>

                                                <svg x-on:click="cancelEditAttachment(attachment)" fill="currentColor"
                                                    class="text-red-500 material-design-icon__svg" width="18" height="18"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2C17.5 2 22 6.5 22 12S17.5 22 12 22 2 17.5 2 12 6.5 2 12 2M12 4C10.1 4 8.4 4.6 7.1 5.7L18.3 16.9C19.3 15.5 20 13.8 20 12C20 7.6 16.4 4 12 4M16.9 18.3L5.7 7.1C4.6 8.4 4 10.1 4 12C4 16.4 7.6 20 12 20C13.9 20 15.6 19.4 16.9 18.3Z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </template>
                                    @endcan

                                    @can('deleteAttachments', App\Models\Document::class)
                                        <template x-if="!attachment.isDeleting && !attachment.isEditing">
                                            <svg x-on:click="deleteAttachment(attachment)" fill="currentColor"
                                                class="material-design-icon__svg text-red-500" width="18" height="18"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z">
                                                </path>
                                            </svg>
                                        </template>
                                        <template x-if="attachment.isDeleting">
                                            <div class="flex items-center gap-1">
                                                <svg x-on:click="confirmDeleteAttachment(attachment)" fill="currentColor"
                                                    class="text-green-500 material-design-icon__svg" width="18" height="18"
                                                    viewBox="0 0 24 24">
                                                    <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"></path>
                                                </svg>
                                                <svg x-on:click="cancelDeleteAttachment(attachment)" fill="currentColor"
                                                    class="text-red-500 material-design-icon__svg" width="18" height="18"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2C17.5 2 22 6.5 22 12S17.5 22 12 22 2 17.5 2 12 6.5 2 12 2M12 4C10.1 4 8.4 4.6 7.1 5.7L18.3 16.9C19.3 15.5 20 13.8 20 12C20 7.6 16.4 4 12 4M16.9 18.3L5.7 7.1C4.6 8.4 4 10.1 4 12C4 16.4 7.6 20 12 20C13.9 20 15.6 19.4 16.9 18.3Z">

                                                    </path>
                                                </svg>
                                            </div>
                                        </template>
                                    @endcan
                                </div>
                            @endcanany

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

            <div x-show="attachmentForm.file" class=" flex items-center gap-2">
                <x-text-input placeholder="Description"
                    class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none" type="text"
                    x-model="attachmentForm.description" x-trap="attachmentForm.file"
                    x-on:keydown.ctrl.enter.prevent="createNewAttachment" />

                <div class=" flex items-center gap-2">

                    <x-svg.save class="text-primary size-4" x-show="attachmentForm.description"
                        x-on:click="createNewAttachment" />

                    <x-svg.cancel class="text-red-500 size-4" x-on:click="resetAttachmentFromExceptDocumentId" />

                </div>
            </div>
        @endcan


    </div>

@endcanany


<script>
    function attachments() {
        return {
            showAttachmentsForm: true,
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
                axios.delete(`documents/${this.attachmentForm.document_id}/attachments/${attachment.id} `)
                    .then((response) => {
                        this.attachments = this.attachments.filter(s => s.id !== attachment.id);
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    })
            },
        }
    }
</script>
