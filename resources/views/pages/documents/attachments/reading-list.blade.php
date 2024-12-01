<template x-if="attachments.length && !fetchingAttachments">
    <div>
        <template x-for="attachment in attachments" :key="attachment.id">
            {{-- Attachment Row --}}
            <div class="flex items-center gap-2 py-1 border-b border-primary border-dashed">
                <a class="text-sm text-gray-900 whitespace-pre-line"
                    x-html="attachment.description" target="__blank" x-bind:href="attachment.file_path"></a>
            </div>
        </template>
    </div>
</template>
