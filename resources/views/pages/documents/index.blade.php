<x-app-layout>
    <div x-data="page()" x-on:steps-updated="fetchRecords" class="flex h-full">

        {{-- List --}}
        <div class="flex-1 mx-auto overflow-auto">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                @include('pages.documents.top')


                <div class="p-2 text-gray-900 flex flex-col gap-2">
                    <template x-for="record in records" :key="record.id">
                        @include('pages.documents.row')
                    </template>

                    {{-- Load More Button --}}
                    <div x-intersect="loadMore" x-show="currentPage < totalPages" class="text-center p-8">
                        <button type="button" class="text-primary font-bold">{{ __('Loading more...') }}</button>
                    </div>
                </div>
            </div>
        </div>

        @include('pages.documents.modal')
    </div>
</x-app-layout>

<script>
    function page() {
        return {
            // ######################### Variables
            route: 'documents',
            contracts: @json($contracts),
            stakeholders: @json($stakeholders),
            users: @json($users),
            filters: {
                search: '',
                contract_ids: [],
                follower_ids: [],
                stakeholder_ids: [],
                statuses: [],
                tag_ids: [],
                types: [],
            },

            fillForm(record, type) {
                this.form = {
                    id: record?.id ?? null,
                    contract_id: record?.contract_id ?? null,
                    from_id: record?.from_id ?? (type === 'outgoing' ? 1 : null),
                    to_id: record?.to_id ?? (type === 'incoming' ? 1 : null),
                    type: record?.type ?? type,
                    title: record?.title ?? '',
                    is_completed: record?.is_completed ?? false,
                    ref: record?.ref ?? '',
                    content: record?.content ?? '',
                    notes: record?.notes ?? '',
                    follow_ids: record?.follow_ids ?? [],
                    tag_ids: record?.tag_ids ?? [],
                };
            },
            // ######################### Variables

            showModal: false,
            records: [],
            form: [],
            currentPage: 1,
            totalPages: 1,

            init() {
                this.$watch('filters', value => {
                    if (value) {
                        this.fetchRecords();
                    }
                });
            },

            get hasFilters() {
                return Object.values(this.filters).some(filter => filter?.length > 0);
            },

            resetFilters() {
                Object.keys(this.filters).forEach(key => this.filters[key] = []);
            },

            fetchRecords(page = 1) {
                axios.get(`/${this.route}/getData`, {
                        params: {
                            page: page,
                            filters: this.filters ?? false,
                        }
                    })
                    .then((response) => {
                        const data = response.data;
                        this.records = [];
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

            resetPageNumber() {
                this.currentPage = 1;
                this.fetchRecords();
            },

            openModal(record = null, type) {
                const isSameRecord = record?.id === this.form.id;
                this.showModal = isSameRecord ? !this.showModal : true;
                record = isSameRecord ? null : record;
                this.fillForm(record, type);
            },

            closeModal() {
                this.openModal(null); // this line to clear selected record before hiding the modal
                this.showModal = false;
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

            toggleRecordIsCompleted(record, isCompleted) {
                axios.put(`/${this.route}/${record.id}/toggle-completed`, {
                        is_completed: isCompleted
                    })
                    .then((response) => {
                        this.fetchRecords();
                        // const data = response.data;
                        // const index = this.records.findIndex(r => r.id === record.id);
                        // if (index !== -1) {
                        //     this.records.splice(index, 1, data);
                        // }
                    })
                    .catch((error) => {
                        alert(error.response.data.message);
                    });
            },

            getName(type, id) {

                item = null

                switch (type) {
                    // case 'type':
                    //     item = this.typesList.find(e => e.id === id)
                    //     break;
                    // case 'status':
                    //     item = this.statusesList.find(e => e.id === id)
                    //     break;
                    case 'user':
                        item = this.users.find(e => e.id === id)
                        break;
                    case 'contract':
                        item = this.contracts.find(e => e.id === id)
                        break;
                    case 'stakeholder':
                        item = this.stakeholders.find(e => e.id === id)
                        break;
                        // case 'tag':
                        //     item = this.tags.find(e => e.id === id)
                        //     break;
                }

                return item ? item.name : "---";
            },

            loadMore() {
                if (this.currentPage < this.totalPages) {
                    this.fetchRecords(this.currentPage + 1);
                }
            }
        }
    }
</script>