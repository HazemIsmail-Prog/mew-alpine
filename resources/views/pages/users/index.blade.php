<x-app-layout>
    <div x-data="page()" class="flex h-full">

        {{-- List --}}
        <div class="flex-1 mx-auto overflow-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                @include('pages.users.top')

                <div class="p-2 text-gray-900 flex flex-col gap-2">
                    <template x-for="record in records" :key="record.id">
                        @include('pages.users.row')
                    </template>

                    {{-- Load More Button --}}
                    <div x-intersect="loadMore" x-show="currentPage < totalPages" class="text-center p-8">
                        <button type="button" class="text-primary font-bold">{{ __('Loading more...') }}</button>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.users.form')
    </div>
</x-app-layout>

<script>
    function page() {
        return {
            route: 'users',
            contracts: @json($contracts),
            stakeholders: @json($stakeholders),
            roles: @json($roles),
            filters: {
                search: ''
            },
            showModal: false,
            records: [],
            form: [],
            currentPage: 1,
            totalPages: 1,
            totalResults: 0,
            loading: false,

            init() {
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
                    name: '',
                    username: '',
                    password: '',
                    contract_ids: [],
                    stakeholder_id: null,
                    role: 'user',
                    is_active: true
                };
            },

            fetchRecords(page = 1) {
                if (this.loading) return;
                this.loading = true;
                axios.get(`/${this.route}/getData`, {
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
