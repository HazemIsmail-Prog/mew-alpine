@props(['label', 'list', 'model', 'multiple' => false, 'live' => false])
<div x-data="{

    expanded: false,
    searchTerm: '',
    list: @js($list),
    selectedItems: $wire.entangle('{{ $model }}') {{ $live ? '.live' : '' }},
    multiple: {{ $multiple ? 'true' : 'false' }},
    live: {{ $live ? 'true' : 'false' }},

    init() {

        // Watch for changes in 'expanded' and log its value
        this.$watch('expanded', value => {
            if (value) {
                this.updateListWidth()
                this.$nextTick(() => this.$refs.searchTermRef.focus());
            }
        });
    },

    updateListWidth() {

        if (this.$refs.list && this.$refs.button) {
            this.$refs.list.style.width = `${this.$refs.button.offsetWidth}px`;
        }
    },

    get filteredList() {
        if (!this.searchTerm) return this.list;
        return this.list.filter(item => {
            return item.name.toLowerCase().includes(this.searchTerm.toLowerCase());
        });
    },

    get sortedItems() {
        return this.filteredList.slice().sort((a, b) => {
            const aSelected = Array.isArray(this.selectedItems) ?
                this.selectedItems.includes(a.id) :
                this.selectedItems === a.id;

            const bSelected = Array.isArray(this.selectedItems) ?
                this.selectedItems.includes(b.id) :
                this.selectedItems === a.id;

            return bSelected - aSelected; // selected items appear on top
        });
    },

    addItem(item) {
        if (this.multiple) {
            const index = this.selectedItems.indexOf(item.id);
            if (index === -1) {
                this.selectedItems.push(item.id);
            } else {
                this.selectedItems.splice(index, 1);
            }
            this.$refs.searchTermRef.focus();
        } else {
            // Single selection mode
            this.selectedItems = item.id;
            this.expanded = false;
        }
        this.$nextTick(() => this.updateListWidth());

        ;
    },

    removeItem(itemId) {
        if (this.multiple) {
            const index = this.selectedItems.indexOf(itemId);
            if (index !== -1) {
                this.selectedItems.splice(index, 1);
            }
        } else {
            // Single selection mode
            this.selectedItems = [];
        }
    },

    isSelected(item) {
        return this.multiple ?
            this.selectedItems.includes(item.id) :
            this.selectedItems === item.id;
    },
}" x-ref="button" class=" w-full overflow-hidden">
    <x-input-label :for="$label" :value="$label" />
    <div id="{{ $label }}" class="w-full overflow-hidden cursor-pointer" x-on:resize.window="updateListWidth"
        x-on:keydown.window="if ($event.key === 'Tab' || $event.key === 'Escape') expanded = false"
        x-on:click.outside="expanded = false">


        <div x-show="!expanded" x-on:click="expanded = true" x-on:click.outside="expanded = false"
            class="flex h-[42px] items-center overflow-auto no-scrollbar gap-1 bg-white p-1  border border-primary rounded-lg">
            <template x-if="multiple && selectedItems.length">
                <template x-for="itemId in selectedItems" :key="itemId">
                    <span type="button" class="bg-primary whitespace-nowrap text-white text-xs px-2 py-1 rounded-md"
                        x-text="list.find(item => item.id === itemId)?.name">
                    </span>
                </template>
            </template>
            <template x-if="!multiple && selectedItems">
                <span class=" text-center w-full" x-text="list.find(item => item.id === selectedItems)?.name"></span>
            </template>
            <template x-if="(!multiple && !selectedItems) || (multiple && !selectedItems.length)">
                <span class=" text-center w-full">---</span>
            </template>
            <!-- Input field -->
        </div>

        <div x-cloak wire:ignore x-anchor.bottom-start-end="$refs.button" x-show="expanded" x-ref="list"
            class=" z-20 bg-white border border-primary rounded-lg shadow-md overflow-clip">
            <input class="w-full p-2 border-none rounded-md focus:outline-none focus:ring-0 outline-none" type="text"
                x-model="searchTerm" x-ref="searchTermRef" x-on:click="expanded=true" />
            <div tabindex="-1" class="bg-white border-t border-primary p-2 max-h-[200px] overflow-y-auto no-scrollbar">
                <template x-for="item in sortedItems" :key="item.id">
                    <div x-bind:class="isSelected(item) ? 'bg-blue-200' : 'hover:bg-primary hover:bg-opacity-10'"
                        x-on:click="addItem(item)" class="select-none cursor-pointer p-1 " x-text="item.name">
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>