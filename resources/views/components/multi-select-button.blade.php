@props([
    'valueIfNull' => '---',
    'label',
    'modalType',
    'list' => null,
    'model',
])

<div class="w-full">
    <x-input-label for="{{ $modalType }}" value="{{ $label }}" />

    <button id="{{ $modalType }}"
        class="bg-white rounded-md p-2 min-h-[44px] text-start w-full flex max-h-32 overflow-y-auto items-center flex-wrap justify-center gap-1 border border-primary"
        x-on:click="openSelectorModal('{{ $modalType }}',{{$list}})">

        <!-- Check if list is an array and has items -->
        <template x-if="Array.isArray({{ $list }}) && {{ $list }}.length > 0">
            <template x-for="item_id in {{ $list }}" :key="item_id">
                <span class="text-xs border rounded-md px-2 py-1 bg-primary text-white"
                    x-text="getName('{{ $model }}', item_id)"></span>
            </template>
        </template>

        <!-- If list is a single item -->
        <template x-if="!Array.isArray({{ $list }}) && {{ $list }}">
            <span
                x-text="getName('{{ $model }}', {{ $list }})"></span>
        </template>

        <!-- Show default value if list is empty or null -->
        <template
            x-if="(!Array.isArray({{ $list }}) && !{{ $list }}) || (Array.isArray({{ $list }}) && {{ $list }}.length === 0)">
            <span>{{ $valueIfNull }}</span>
        </template>
    </button>
    <x-input-error :messages="$errors->get('{{ $list }}')" class="mt-2" />

</div>
