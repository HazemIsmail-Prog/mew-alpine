{{-- Search and Create New Button --}}
<div class="flex items-center justify-between gap-3 my-3 px-4">

    <p>{{ __('Results') }}: <span x-text="totalResults"></span></p>

    @can('create', App\Models\Document::class)
        <div class=" flex items-center gap-3">
            <button x-on:click="openModal(null,'outgoing')" type="button"
                class="flex items-center justify-center gap-1 h-9 py-1 w-28 rounded-lg text-white font-bold !bg-danger px-4">
                <x-svg.outbox class=" size-6" />
                <span class="hidden md:block">
                    {{ __('Outgoing') }}
                </span>
            </button>
            <button x-on:click="openModal(null,'incoming')" type="button"
                class="flex items-center justify-center gap-1 h-9 py-1 w-28 rounded-lg text-white font-bold !bg-success px-4">
                <x-svg.inbox class=" size-6" />
                <span class="hidden md:block">
                    {{ __('Incoming') }}
                </span>
            </button>
        </div>
    @endcan
</div>

<div class=" p-2 border border-dashed border-primary rounded-md mt-2 flex flex-col lg:flex-row items-start gap-2">
    <div class="w-full">
        <x-input-label for="filterSearch" value="{{ __('Search') }}" />
        <input id="filterSearch" x-model="filters.search" type="text"
            class="w-full border border-primary p-2 rounded-md">
    </div>

    <x-dropdown-test id="filters.contract_ids" label="{{ __('Contract') }}" :list="$contracts" :multiple="true"
        model="filters.contract_ids" selected-items="filters.contract_ids" />

    <x-dropdown-test id="filters.stakeholder_ids" label="{{ __('Stakeholder') }}" :list="$stakeholders" :multiple="true"
        model="filters.stakeholder_ids" selected-items="filters.stakeholder_ids" />

    <x-dropdown-test id="filters.follower_ids" label="{{ __('Follwers') }}" :list="$users" :multiple="true"
        model="filters.follower_ids" selected-items="filters.follower_ids" />

    <x-dropdown-test id="filters.tag_ids" label="{{ __('Tag') }}" :list="$tags" :multiple="true"
        model="filters.tag_ids" selected-items="filters.tag_ids" />


    <x-dropdown-test id="filters.types" label="{{ __('Type') }}" :list="$typesList" :multiple="true"
        model="filters.types" selected-items="filters.types" />

    <x-dropdown-test id="filters.statuses" label="{{ __('Status') }}" :list="$statusesList" :multiple="true"
        model="filters.statuses" selected-items="filters.statuses" />

    <button x-cloak x-bind:disabled="!hasFilters" x-on:click="resetFilters()"
        class="text-primary disabled:text-gray-400 disabled:border-gray-400 disabled:cursor-not-allowed self-end border h-[42px] whitespace-nowrap border-primary p-2 rounded-md">
        {{ __('Reset') }}
    </button>
</div>
