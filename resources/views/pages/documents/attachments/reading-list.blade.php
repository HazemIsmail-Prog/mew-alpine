<template x-if="attachments.length && !fetchingAttachments">
    <div>
        <template x-for="(attachment, index) in attachments" :key="attachment.id">
            {{-- Attachment Row --}}
            <div class="py-1"
                x-bind:class="{
                    'border-b border-primary border-dashed': index !== attachments.length - 1,
                    'text-gray-900': attachment.is_completed,
                    'text-red-500': !attachment.is_completed,
                }">
                <a class="text-sm text-gray-900 whitespace-pre-line" x-html="attachment.description" target="__blank"
                    x-bind:href="attachment.file_path"></a>
            </div>
        </template>
    </div>
</template>
