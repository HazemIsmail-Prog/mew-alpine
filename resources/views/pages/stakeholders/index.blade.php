<x-app-layout>
    <div x-data="page()" class="flex h-full">

        {{-- List --}}
        <div class="flex-1 mx-auto overflow-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                @include('pages.stakeholders.top')

                <div class="p-2 text-gray-900 flex flex-col gap-2">
                    <template x-for="record in records" :key="record.id">
                        @include('pages.stakeholders.row')
                    </template>

                    {{-- Load More Button --}}
                    <div x-intersect="loadMore" x-show="currentPage < totalPages" class="text-center p-8">
                        <button type="button" class="text-primary font-bold">{{ __('Loading more...') }}</button>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.stakeholders.form')
    </div>
</x-app-layout>

<script>
    function page() {
        return {
            // ######################### Variables
            route: 'stakeholders',
            filters: {},
            defaultFilters: {
                search: '',
            },

            fillForm(record) {
                this.form = {
                    id: record?.id ?? null,
                    name: record?.name ?? '',
                    is_active: record?.is_active ?? true
                };
            },
            // ######################### Variables

            showModal: false,
            records: [],
            form: [],
            currentPage: 1,
            totalPages: 1,

            init() {
                axios.defaults.headers.common["X-CSRF-TOKEN"] = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");
                this.filters = {
                    ...this.defaultFilters
                };
                this.fetchRecords();
            },

            fetchRecords(page = 1) {
                axios.get(`/${this.route}/getData`, {
                        params: {
                            page: page,
                            filters: this.filters,
                        }
                    })
                    .then((response) => {
                        const data = response.data;
                        if (page === 1) {
                            this.records = data.data;
                        } else {
                            this.records = [...this.records, ...data.data];
                        }
                        this.currentPage = data.current_page;
                        this.totalPages = data.last_page;
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            resetFilters() {
                this.filters = {
                    ...this.defaultFilters
                };
                this.resetPageNumber();
            },

            resetPageNumber() {
                this.currentPage = 1;
                this.fetchRecords();
            },

            openModal(record = null) {
                const isSameRecord = record?.id === this.form.id;
                this.showModal = isSameRecord ? !this.showModal : true;
                this.fillForm(record);
            },

            submitRecord() {
                const url = this.form.id ? `/${this.route}/${this.form.id}` : `/${this.route}`;
                const method = this.form.id ? 'put' : 'post';
                axios({
                        method: method,
                        url: url,
                        data: this.form
                    })
                    .then((response) => {
                        const data = response.data;
                        if (this.form.id) {
                            // Update the record in the records list
                            const index = this.records.findIndex(record => record.id === data.id);
                            if (index !== -1) {
                                this.records.splice(index, 1, data);
                            }
                        } else {
                            // Add the new record to the records list
                            this.resetFilters();
                            this.records.unshift(data);
                            this.openModal(data);
                        }
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            deleteRecord(record) {
                if (!confirm('Are you sure you want to delete this record?'))
                    return;
                axios.delete(`/${this.route}/${record.id}`)
                    .then(() => {
                        this.showModal = false;
                        this.fetchRecords()
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            toggleRecordIsActive(record, isActive) {
                axios.put(`/${this.route}/${record.id}/toggle-active`, {
                        is_active: isActive
                    }).then((response) => {
                        const data = response.data;
                        // Update the record in the records list
                        const index = this.records.findIndex(r => r.id === record.id);
                        if (index !== -1) {
                            this.records.splice(index, 1, data);
                        }
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            loadMore() {
                if (this.currentPage < this.totalPages) {
                    this.fetchRecords(this.currentPage + 1);
                }
            }
        }
    }
</script>
