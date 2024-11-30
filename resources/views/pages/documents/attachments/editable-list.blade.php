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