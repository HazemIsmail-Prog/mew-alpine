<div 
    wire:key="document-{{ $document->id }} - {{ rand() }}" 
    @class([
        'flex items-center gap-3 w-full lg:w-3/5 ',
        'self-end' => $document->type == 'incoming',
    ])
>

    @can('update', $document)
        <div>
            <label x-on:change="toggleDocumentCompleted({{ $document }},$event.target.checked)"
                class="inline-flex items-center cursor-pointer">
                <input @checked($document->is_completed) type="checkbox" class="hidden peer">
                <div
                    class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary">
                </div>
            </label>
        </div>
    @endcan



    <div x-on:click="openDocumentForm({{ $document }})"
        x-bind:class="[
            'flex-1 flex items-center justify-between cursor-pointer  border rounded-lg p-2',
            documentForm.id == {{ $document->id }} ?
            'bg-primary !hover:opacity-100 text-white' :
            'bg-zinc-100 hover:bg-primary hover:bg-opacity-25',
        ]">
        <div>
            <div class=" font-extrabold text-lg whitespace-pre-line">{!! $document->title !!}
            </div>
            <div class=" flex items-center gap-3">
                @if ($document->type == 'incoming')
                    <svg fill="currentColor" class="material-design-icon__svg text-success" width="20" height="20"
                        viewBox="0 0 24 24">
                        <path
                            d="M2 12H4V17H20V12H22V17C22 18.11 21.11 19 20 19H4C2.9 19 2 18.11 2 17V12M12 15L17.55 9.54L16.13 8.13L13 11.25V2H11V11.25L7.88 8.13L6.46 9.55L12 15Z">
                            <!---->
                        </path>
                    </svg>
                @else
                    <svg fill="currentColor" class="material-design-icon__svg text-danger" width="20" height="20"
                        viewBox="0 0 24 24">
                        <path
                            d="M2 12H4V17H20V12H22V17C22 18.11 21.11 19 20 19H4C2.9 19 2 18.11 2 17V12M12 2L6.46 7.46L7.88 8.88L11 5.75V15H13V5.75L16.13 8.88L17.55 7.45L12 2Z">
                            <!---->
                        </path>
                    </svg>
                @endif
                <div class=" flex flex-col">
                    <div class="text-sm" x-text="getName('contract',{{ $document->contract_id }})">
                    </div>
                    @if ($document->type == 'incoming')
                        <div class="text-sm" x-text="getName('stakeholder',{{ $document->from_id }})">
                        </div>
                    @else
                        <div class="text-sm" x-text="getName('stakeholder',{{ $document->to_id }})">
                        </div>
                    @endif
                </div>
            </div>
            <div class=" mt-1 flex gap-1 items-start">
                @if ($document->users)
                    @foreach ($document->follow_ids as $id)
                        <div class="text-sm rounded-md px-3 bg-primary text-white"
                            x-text="getName('user',{{ $id }})"></div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class=" flex flex-col gap-1 items-start">
            @foreach ($document->uncompletedSteps as $step)
                <span
                    class="whitespace-pre-line cursor-pointer bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">{{ $step->action }}</span>
            @endforeach
        </div>
    </div>


    @can('delete', $document)
        <x-svg.delete 
            class=" text-primary size-6" 
            wire:confirm="Sure?" 
            wire:click="deleteDocument({{ $document }})" 
        />
    @endcan

</div>
