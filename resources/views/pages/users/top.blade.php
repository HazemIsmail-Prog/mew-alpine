{{-- Search and Create New Button --}}
<div class="flex items-center gap-3 my-3 px-4">
    <input type="text" x-model="filters.search" placeholder="Search by name"
        class="flex-1 px-4 py-2 rounded-md border border-gray-300 focus:ring-primary focus:border-primary"
        x-on:input.debounce.500ms="resetPageNumber">

    <button x-on:click="resetFilters" type="button"
        class="h-9 py-1 px-4 rounded-lg text-primary border-primary border font-bold">
        {{ __('Reset Filters') }}
    </button>

    <button x-on:click="openModal" type="button"
        class="flex items-center justify-center gap-1 h-9 py-1 w-28 rounded-lg text-white font-bold !bg-danger px-4">
        {{ __('New') }}
    </button>
</div>
