<x-app-layout>
    <div x-data="vacationsComponent()" class="flex h-full">
        <div class="flex-1 mx-auto overflow-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <!-- Search and Create New Button -->
                <div class="flex items-center justify-end gap-3 my-3 px-4">
                    <input type="date" x-model="filters.date"
                        class="flex-1 px-4 py-2 rounded-md border border-gray-300 focus:ring-primary focus:border-primary">
                    <!-- <button x-on:click="resetFilters" type="button"
                        class="h-9 py-1 px-4 rounded-lg text-primary border-primary border font-bold">
                        {{ __('Reset Filters') }}
                    </button> -->

                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'superAdmin')
                        <button x-on:click="openModal()" type="button"
                            class="flex items-center justify-center gap-1 h-9 py-1 w-28 rounded-lg text-white font-bold !bg-danger px-4">
                            {{ __('New') }}
                        </button>
                    @endif
                </div>


                <!-- Records -->
                <div class="p-2 text-gray-900 flex flex-col gap-2">
                    <template x-for="record in records" :key="record.id">
                        <div class="flex items-center gap-3">
                            <div x-on:click="record.can_update ? openModal(record) : null"
                                class="flex-1 flex flex-col gap-1 items-start cursor-pointer border rounded-lg p-2 bg-zinc-100 hover:bg-primary hover:bg-opacity-25"
                                x-bind:class="{
                                    '!bg-primary !hover:bg-primary !hover:bg-opacity-25 text-white': record.id == form.id,
                                    '!bg-zinc-100 hover:bg-primary hover:bg-opacity-25': record.id != form.id,
                                }"
                                >
                                <div class="font-extrabold text-lg whitespace-pre-line" x-html="getUserById(record.user_id).name"></div>
                                <div class="flex items-center gap-2">
                                    <div 
                                        class="text-sm px-3 py-1 rounded-md"
                                        x-bind:class="{
                                            '!bg-green-600 text-white': record.is_current,
                                            '!bg-gray-500 text-white': record.is_future,
                                            '!bg-red-600 text-white line-through': record.is_past,
                                        }"
                                        x-text="record.formatted_start_date">
                                    </div>
                                    <div 
                                        class="text-sm px-3 py-1 rounded-md"
                                        x-bind:class="{
                                            '!bg-green-600 text-white': record.is_current,
                                            '!bg-gray-500 text-white': record.is_future,
                                            '!bg-red-600 text-white line-through': record.is_past,
                                        }"
                                        x-text="record.formatted_end_date">
                                    </div>
                                </div>
                            </div>
                            <template x-if="record.can_delete">
                                <x-svg.delete x-on:click="deleteRecord(record)" class="text-primary size-6" />
                            </template>
                        </div>
                    </template>
                    <div x-cloak x-intersect="loadMore" x-show="currentPage < totalPages" class="text-center p-8">
                        <button type="button" class="text-primary font-bold">{{ __('Loading more...') }}</button>
                    </div>
                </div>
            </div>
        </div>
        @include('pages.vacations.form')
    </div>
</x-app-layout>

<script>
    function vacationsComponent() {
        return {
            route: 'vacations',
            filters: {
                date: ''
            },
            showModal: false,
            records: [],
            form: [],
            currentPage: 1,
            totalPages: 1,
            totalResults: 0,
            loading: false,
            users: @json($users),
            init() {
                axios.defaults.headers.common["X-CSRF-TOKEN"] = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");
                this.fetchRecords();
                this.$watch(() => JSON.stringify(this.filters), () => {
                    this.resetPageNumber();
                });
            },

            resetFilters() {
                Object.keys(this.filters).forEach(key => this.filters[key] = []);
                this.fetchRecords(); // Reload records after resetting filters
            },

            fillForm(record) {
                this.form = record ? {
                    ...record
                } : this.getEmptyForm();
            },

            getEmptyForm() {
                return {
                    id: null,
                    user_id: null,
                    start_date: '',
                    end_date: '',
                    reason: '',
                    type: 'annual',
                };
            },

            getUserById(id) {
                return this.users.find(user => user.id === id);
            },

            fetchRecords(page = 1) {
                if (this.loading) return;
                this.loading = true;
                axios.get(`/${this.route}`, {
                        params: {
                            page,
                            filters: this.filters
                        }
                    })
                    .then((response) => {
                        const data = response.data;
                        this.records = page === 1 ? data.data : [...this.records, ...data.data];
                        this.currentPage = data.meta.current_page;
                        this.totalPages = data.meta.last_page;
                        this.totalResults = data.meta.total;
                    })
                    .catch((error) => console.error(error.response?.data?.message || error.message))
                    .finally(() => this.loading = false);
            },

            openModal(record = null) {
                this.showModal = true;
                this.fillForm(record);
            },

            closeModal() {
                this.fillForm(this.getEmptyForm());
                this.showModal = false;
            },

            submitRecord() {
                const url = this.form.id ? `/${this.route}/${this.form.id}` : `/${this.route}`;
                const method = this.form.id ? 'put' : 'post';
                axios({
                        method,
                        url,
                        data: this.form
                    })
                    .then((response) => {                        
                        this.fetchRecords(); // Refresh records
                        this.openModal(response.data.data)
                    })
                    .catch((error) => {
                        console.error(error.response?.data?.message || error.message);
                        alert('Failed to save record. Please check your input.');
                    });
            },

            loadMore() {
                if (this.currentPage >= this.totalPages || this.loading) return;
                this.fetchRecords(this.currentPage + 1);
            },

            resetPageNumber() {
                this.currentPage = 1;
                this.fetchRecords();
            },
            toggleRecordIsActive(record, isActive) {
                axios.put(`/${this.route}/${record.id}/toggle-active`, {
                        is_active: isActive
                    })
                    .then((response) => {
                        const data = response.data;
                        // Update the record in the records list
                        const index = this.records.findIndex(r => r.id === record.id);
                        if (index !== -1) {
                            this.records.splice(index, 1, data);
                        }
                    })
                    .catch((error) => {
                        console.error(error.response?.data?.message || error.message);
                        alert('Failed to update active status.');
                    });
            },

            deleteRecord(record) {
                if (!confirm('Are you sure you want to delete this record?')) return;

                axios.delete(`/${this.route}/${record.id}`)
                    .then(() => {
                        // Remove the record from the list
                        this.records = this.records.filter(r => r.id !== record.id);
                        this.totalResults -= 1; // Update total results
                    })
                    .catch(error => {
                        console.error(error.response?.data?.message || error.message);
                        alert('Failed to delete the record.');
                    });
            },
        };
    }
</script>
