<x-app-layout>
    <div x-data="documentGenerator()" class="flex h-full p-3 gap-3">

        <div class="w-1/4 flex flex-col gap-3 border rounded-lg p-3 overflow-y-auto">
            <input type="text" x-model="filters.search">

            <button x-on:click="openModal()"
                class="bg-primary cursor-pointer border rounded-lg p-2 text-white">{{ __('New Letter') }}</button>

            <template x-for="record in records" :key="record.id">
                <div class="flex items-center gap-3 w-full select-none">

                    <div x-on:click="openModal(record)"
                        x-bind:class="['flex-1 flex flex-col md:flex-row items-center justify-between gap-2 cursor-pointer border rounded-lg p-2',
                            form.id == record.id ? 'bg-primary !hover:opacity-100 text-white' :
                            'bg-zinc-100 hover:bg-primary hover:bg-opacity-25'
                        ]">
                        <div class=" w-full text-sm">
                            <div class="whitespace-pre-line font-bold" x-html="record.subject"></div>
                            <div x-text="record.sender"></div>
                            <div x-text="record.receiver"></div>
                        </div>
                    </div>


                    <div x-data="{ show: false }" x-on:mouseenter="show=true" x-on:mouseleave="show=false">
                        <x-svg.print x-ref="button" class="text-primary size-6" />
                        <div x-anchor="$refs.button" x-show="show"
                            class=" flex flex-col z-10 bg-white shadow-md rounded-lg overflow-clip">
                            <a target="__blank" x-bind:href="`letters/${record.id}/normal-pdf`"
                                class=" text-center py-2 px-4 hover:bg-primary hover:text-white">{{ __('Normal') }}</a>
                            <a target="__blank" x-bind:href="`letters/${record.id}/original-pdf`"
                                class=" text-center py-2 px-4 hover:bg-primary hover:text-white">{{ __('Original') }}</a>
                        </div>
                    </div>
                    <x-svg.duplicate x-on:click="duplicate(record)" class="text-primary size-6" />
                    <x-svg.delete x-on:click="deleteRecord(record)" class="text-primary size-6" />


                </div>
            </template>
        </div>


        <div x-show="showModal" class="w-1/4 flex flex-col gap-3 border rounded-lg p-3 overflow-y-auto">
            <label for="internal">
                <input id="internal" type="checkbox" x-bind:checked="form.internal" x-model="form.internal">
                <span>مذكرة داخلية</span>
            </label>
            <input type="text" placeholder="From" x-model="form.sender" class="border p-2 rounded">
            <div class=" flex w-full gap-1">
                <select x-model="form.prefix">
                    <option value="السيد">السيد</option>
                    <option value="السادة">السادة</option>
                </select>
                <input type="text" placeholder="To" x-model="form.receiver" class="border p-2 rounded flex-1">
            </div>
            <label for="official">
                <input id="official" type="checkbox" x-bind:checked="form.official" x-model="form.official">
                <span>بالطريق الرسمي</span>
            </label>
            <textarea placeholder="العنوان" x-model="form.address" rows="3"></textarea>
            <textarea placeholder="subject" x-model="form.subject" rows="5" class="border p-2 rounded"></textarea>
            <textarea placeholder="Body" id="body" x-model="form.body" rows="10" class="border p-2 rounded"></textarea>
            <label for="code">
                <input id="code" type="checkbox" x-bind:checked="form.code" x-model="form.code">
                <span>كود ترميز القطاع</span>
            </label>
            <label for="has_attachments">
                <input id="has_attachments" type="checkbox" x-bind:checked="form.has_attachments"
                    x-model="form.has_attachments">
                <span>مرفقات</span>
            </label>
            <textarea placeholder="Copy to" x-model="form.copyTo" rows="5" class="border p-2 rounded"></textarea>


            <x-primary-button x-on:click="submitRecord"
                class=" text-center justify-center">{{ __('Save') }}</x-primary-button>

        </div>


        <div x-show="showModal" class="w-1/2 border rounded-lg p-3 overflow-auto">
            <div id="previewContent" class=" flex flex-col mx-auto bg-white pb-20 w-[210mm] min-h-[296mm]">

                <div class="mt-48"></div>

                <div class="content-wrapper justify-between mx-20">
                    <p x-show="form.internal" class="text-center text-2xl font-bold mb-2 font-sultann">مذكرة داخلية</p>

                    <div x-show="form.receiver" class="flex gap-2 font-sultanb text-2xl">
                        <p x-text="form.prefix"></p>
                        <p>/</p>
                        <p x-text="form.receiver"></p>
                        <p x-text="suffix" class="ms-auto me-10"></p>
                    </div>
                    <p x-show="form.address" x-html="form.address" class="font-sultann text-xl"></p>

                    <p x-show="form.official" class="font-sultanb text-xl">بالطريق الرسمي</p>

                    <p x-show="form.receiver" class="font-sultanb text-xl">تحية طيبة وبعد،،،</p>

                    <div
                        class="mt-5 pb-3 w-fit max-w-[75%] mx-auto text-center font-sultanb font-bold text-xl border-b border-black">
                        <span x-html="form.subject" class="whitespace-pre-line"></span>
                    </div>

                    <div class="mt-5 text-justify  font-sultann text-lg leading-10" style="text-indent: 3rem;">
                        <span x-html="form.body" class="whitespace-pre-line"></span>
                    </div>

                    <div x-show="form.body" class=" mt-5 font-sultanb text-xl text-center">وتفضلوا بقبول وافر الاحترام والتقدير</div>

                    <div x-html="form.sender" class=" mt-5 pb-16 font-sultanb text-2xl w-2/6 text-center ms-auto">
                    </div>

                </div>

                <div class="text-xs font-sultann mx-20 mt-auto">
                    <p x-show="form.code">كود ترميز القطاع (15000)</p>
                    <p x-show="form.has_attachments">المرفقات: كما ورد أعلاه</p>
                    <p x-show="form.copyTo">نسخة لكل من:</p>
                    <pre x-show="form.copyTo" x-html="form.copyTo" class=" ms-5 font-sultann"></pre>
                    <pre x-show="form.copyTo" class=" ms-5 font-sultann">الملف - <span x-text="form.id"></span></pre>
                </div>

            </div>
            {{-- </div> --}}
        </div>
    </div>

    <script>
        function documentGenerator() {
            return {
                route: 'letters',
                filters: {
                    search: '',
                },

                fillForm(record, type) {
                    this.form = {
                        id: record?.id ?? null,
                        code: record?.code ?? false,
                        internal: record?.internal ?? false,
                        official: record?.official ?? false,
                        has_attachments: record?.has_attachments ?? true,
                        address: record?.address ?? '',
                        sender: record?.sender ?? '',
                        receiver: record?.receiver ?? '',
                        subject: record?.subject ?? '',
                        body: record?.body ?? '',
                        copyTo: record?.copyTo ?? '',
                        prefix: record?.prefix ?? 'السيد',
                    };
                },
                showModal: false,
                records: [],
                form: [],
                currentPage: 1,
                totalPages: 1,
                totalResults: 0,

                init() {
                    axios.defaults.headers.common["X-CSRF-TOKEN"] = document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content");

                    this.fetchRecords();

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

                fetchRecord(id) {
                    axios.get(`/${this.route}/${id}`)
                        .then((response) => {
                            const updatedRecord = response.data.data;
                            // Find the record in the records array and update it
                            const index = this.records.findIndex(record => record.id === updatedRecord.id);
                            if (index !== -1) {
                                this.records.splice(index, 1, updatedRecord); // Replace the existing record
                            }
                        })
                        .catch((error) => {
                            alert(error.response.data.message);
                        });
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

                            if (page === 1) {
                                this.records = data.data;
                            } else {
                                this.records = [...this.records, ...data.data];
                            }
                            this.currentPage = data.meta.current_page;
                            this.totalPages = data.meta.last_page;
                            this.totalResults = data.meta.total;
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
                    record = isSameRecord ? null : record;
                    this.showModal = isSameRecord ? !this.showModal : true;
                    this.fillForm(record, type);

                },

                duplicate(record) {
                    this.closeModal();
                    this.openModal(record);
                    this.form.id = null;
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

                            const data = response.data.data;

                            if (this.form.id) {
                                if (this.hasFilters) {
                                    this.fetchRecords();
                                } else {
                                    // Update the record in the records list
                                    const index = this.records.findIndex(record => record.id === data.id);
                                    if (index !== -1) {
                                        this.records.splice(index, 1, data);
                                    }
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
                            // Find the index of the record in the array
                            const index = this.records.findIndex(r => r.id === record.id);
                            if (index !== -1) {
                                // Remove the record from the array
                                this.records.splice(index, 1);
                                this.totalResults--; // Decrease the total count
                            }
                            this.showModal = false; // Close the modal if it is open
                        })
                        .catch((error) => {
                            alert(error.response.data.message);
                        });
                },

                loadMore() {
                    if (this.currentPage < this.totalPages) {
                        this.fetchRecords(this.currentPage + 1);
                    }
                },




                get suffix() {
                    switch (this.form.prefix) {
                        case 'السيد':
                            return 'المحترم'
                            break
                        case 'السادة':
                            return 'المحترمين'
                            break
                    }
                },

                printPreview() {
                    // Get the preview content element
                    const printContent = document.getElementById('previewContent').outerHTML;

                    // Create a new window
                    const printWindow = window.open('', '', 'width=800,height=600');
                },


            };
        }
    </script>
</x-app-layout>
