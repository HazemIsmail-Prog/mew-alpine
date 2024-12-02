@props(['id', 'label', 'list' => [], 'multiple' => false, 'model', 'selectedItems'])

<div x-data="{
    multiple: {{ $multiple ? 'true' : 'false' }},
    expanded: false,
    searchTerm: '',
    selectedItems: {{ $selectedItems }},
    focusedItemIndex: null,
    list: @js($list),
    init() {
        this.$watch('expanded', value => {
            if (value) {
                this.updateListWidth();
                this.focusedItemIndex = null;
                this.$nextTick(() => {
                    this.$refs.searchTermRef.focus()
                    this.$refs.scrollContainer.scrollTop = 0;                
                });
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
        return this.list.filter(item => item.name.toLowerCase().includes(this.searchTerm.toLowerCase()));
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
            this.selectedItems = item.id;
            this.expanded = false;
        }
        this.$nextTick(() => {
        this.updateListWidth();
        this.searchTerm = '';
        });
    },
    isSelected(item) {
        return this.multiple ?
            this.selectedItems?.includes(item.id) :
            this.selectedItems === item.id;
    },
    handleKeydown(e) {
        if (!this.expanded) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (this.focusedItemIndex === null) {
                this.focusedItemIndex = 0; // Start at the first item
                this.scrollToFocusedItem();
            } else if (this.focusedItemIndex < this.sortedItems.length - 1) {
                this.focusedItemIndex++; // Move down if not at the last item
                this.scrollToFocusedItem();
            }

        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (this.focusedItemIndex === null) {
                this.focusedItemIndex = 0; // Start at the first item
                this.scrollToFocusedItem();
            } else if (this.focusedItemIndex > 0) {
                this.focusedItemIndex--; // Move up if not at the first item
                this.scrollToFocusedItem();
            }

        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (this.focusedItemIndex !== null) {
                const item = this.sortedItems[this.focusedItemIndex];
                this.addItem(item);
            }
        }
    },
    scrollToFocusedItem() {
        this.$nextTick(() => {
            const scrollContainer = this.$refs.scrollContainer;
            const focusedItem = document.querySelector('.focused'); // Select the focused item

            if (scrollContainer && focusedItem) {
                const containerTop = scrollContainer.scrollTop;
                const containerHeight = scrollContainer.offsetHeight;
                const itemTop = focusedItem.offsetTop;
                const itemHeight = focusedItem.offsetHeight;

                if ((itemTop + itemHeight) > containerHeight) {
                    scrollContainer.scrollTop = itemTop - (containerHeight - itemHeight);
                }

                if (containerTop < itemHeight) {
                    scrollContainer.scrollTop = itemTop - (containerHeight - itemHeight) + itemHeight - containerTop;
                }
            }
        });
    },
}" x-ref="button" class="w-full overflow-hidden" x-model="selectedItems"
    x-modelable="{{ $model }}">
    <x-input-label :for="$id" :value="$label" />

    <div id="{{ $id }}" class="w-full overflow-hidden cursor-pointer" x-on:resize.window="updateListWidth"
        x-on:keydown.window="if ($event.key === 'Tab' || $event.key === 'Escape') expanded = false"
        x-on:click.outside="expanded = false">
        <div x-show="!expanded" x-on:click="expanded = true"
            class="flex h-[42px] items-center gap-1 bg-white p-1 border border-primary rounded-lg">
            <template x-if="multiple && selectedItems?.length">
                <template x-for="itemId in selectedItems" :key="itemId">
                    <span class="bg-primary whitespace-nowrap text-white text-xs px-2 py-1 rounded-md"
                        x-text="list.find(item => item.id === itemId)?.name"></span>
                </template>
            </template>
            <template x-if="!multiple && selectedItems">
                <span class="text-center w-full" x-text="list.find(item => item.id === selectedItems)?.name"></span>
            </template>
            <template x-if="(!multiple && !selectedItems) || (multiple && !selectedItems?.length)">
                <span class="text-center w-full">---</span>
            </template>
        </div>

        <div x-cloak x-anchor.bottom-start-end="$refs.button" x-show="expanded" x-ref="list"
            class="z-20 bg-white border border-primary rounded-lg shadow-md overflow-clip">
            <input x-on:keydown="handleKeydown($event)"
                class="w-full p-2 border-none rounded-md focus:outline-none focus:ring-0 outline-none" type="text"
                x-model="searchTerm" x-ref="searchTermRef" />
            {{-- want this div to scroll to focused item --}}
            <div x-ref="scrollContainer" tabindex="-1"
                class="bg-white border-t border-primary p-2 max-h-[300px] overflow-y-auto no-scrollbar">
                <template x-for="(item, index) in sortedItems" :key="item.id">
                    <div x-bind:class="[
                        isSelected(item) ? 'bg-blue-200' : 'hover:bg-primary hover:bg-opacity-10',
                        focusedItemIndex === index && expanded ? 'focused bg-primary text-white rounded-md px-2 shadow-lg' : ''
                    ]"
                        x-on:click="addItem(item)" class="select-none cursor-pointer p-1" x-text="item.name"></div>
                </template>
            </div>
        </div>
    </div>
</div>
