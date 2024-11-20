<div class=" p-2 border border-dashed border-primary rounded-md mt-2 flex flex-col lg:flex-row items-start gap-2">
    <div class="w-full">
        <x-input-label for="filterSearch" value="{{ __('Search') }}" />
        <input id="filterSearch" x-model="filters.search" type="text" class="w-full border border-primary p-2 rounded-md">
    </div>

    <x-test label="{{ __('Contract') }}" :list="$this->contracts" model="filters.contract_ids" multiple live />
    <x-test label="{{ __('Stakeholder') }}" :list="$this->stakeholders" model="filters.stakeholder_ids" multiple live />
    <x-test label="{{ __('Follower') }}" :list="$this->users" model="filters.follower_ids" multiple live />
    <x-test label="{{ __('Tags') }}" :list="$this->tags" model="filters.tag_ids" multiple live />
    <x-test label="{{ __('Type') }}" :list="$this->typesList" model="filters.types" multiple live />
    <x-test label="{{ __('Status') }}" :list="$this->statusesList" model="filters.statuses" multiple live />

    <button x-cloak x-show="hasFilters" x-on:click="resetFilters()"
        class="text-primary self-end border h-[42px] whitespace-nowrap border-primary p-2 rounded-md">
        {{ __('Reset') }}
    </button>
</div>
