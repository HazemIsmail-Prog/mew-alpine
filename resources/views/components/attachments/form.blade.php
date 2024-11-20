@can('createAttachments', App\Models\Document::class)

    <div 
        class="flex items-center justify-center w-full" 
        x-show="!attachmentForm.file" 
        x-on:dragover.prevent
        x-on:dragleave.prevent x-on:drop.prevent="handleFileDrop($event)"
    >

        <label 
            for="dropzone-file"
            class="flex flex-col items-center justify-center w-full h-14 border border-primary border-dashed rounded-lg cursor-pointer"
        >

            <p class="text-sm text-primary select-none">
                <span class="font-semibold">
                    {{ __('Click to upload') }}
                </span>
                {{ __('or drag and drop') }}
            </p>
            
            <input 
                id="dropzone-file" 
                type="file" 
                class="hidden"
                x-ref="uploadInput" 
                x-on:change="handleFileUpload($event)" 
            />

        </label>

    </div>

    <div x-show="attachmentForm.file" class=" flex items-center gap-2">
        <x-text-input 
            placeholder="Description" 
            class="w-full rounded-md focus:outline-none focus:ring-0 border-none outline-none" type="text"
            x-model="attachmentForm.description"
            x-trap="attachmentForm.file"
            x-on:keydown.ctrl.enter.prevent="createNewAttachment" 
        />

        <div class=" flex items-center gap-2">

            <x-svg.save
                class="text-primary size-4" 
                x-show="attachmentForm.description" 
                x-on:click="createNewAttachment" 
            />

            <x-svg.cancel 
                class="text-red-500 size-4" 
                x-on:click="resetAttachmentFromExceptDocumentId" 
            />

        </div>
    </div>
@endcan
