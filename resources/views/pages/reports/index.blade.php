<x-app-layout>
    <div x-data="reportComponent()" class="flex h-full p-3 gap-3 ">


        <!-- contracts list and search -->
        <div class="print:hidden w-1/2 md:w-1/4 shrink-0 flex flex-col gap-3 border rounded-lg p-3 overflow-y-auto">
            <div class="flex items-center gap-1">
                <input class="w-full" type="text" placeholder="{{ __('Search') }}" x-model="filters.search">
            </div>

            <template x-for="record in records" :key="record.id">
                <div class="flex items-center gap-3 w-full select-none">

                    <div x-on:click="setSelectedRecord(record)"
                        x-bind:class="['flex-1 flex flex-col md:flex-row items-center justify-between gap-2 cursor-pointer border rounded-lg p-2',
                            selectedRecord?.id == record.id ? 'bg-primary !hover:opacity-100 text-white' :
                            'bg-zinc-100 hover:bg-primary hover:bg-opacity-25'
                        ]">
                        <div class=" w-full text-sm">
                            <div class="whitespace-pre-line font-bold" x-html="record.name"></div>
                        </div>
                    </div>
                </div>
            </template>

        </div>


        <!-- selected contract details -->
        <template x-if="selectedRecord">
            <div class="flex-1 flex flex-col items-center gap-3 w-full overflow-y-auto">

                <!-- contract name -->
                <div class="whitespace-pre-line font-bold" x-text="selectedRecord?.name"></div>

                <!-- info table -->
                <h2 class="text-lg font-bold w-full text-start">{{ __('Information') }}</h2>
                <table class="w-full">
                    <template x-if="infos.length > 0">
                        <template x-for="info in infos" :key="info.id">
                            <tr class="even:bg-zinc-100 odd:bg-white">
                                <th class="text-start border  p-1 w-[20%]">
                                    <span x-show="!editingInfo || editingInfo?.id !== info.id" class="text-sm print:text-xs whitespace-pre-line" x-html="info.key"></span>
                                    <template x-if="editingInfo?.id === info.id">
                                        <textarea class="w-full" x-model="editingInfo.key" rows="1"></textarea>
                                    </template>
                                </th>
                                <td class="text-start border  p-1 w-full">
                                    <span x-show="!editingInfo || editingInfo?.id !== info.id" class="text-sm print:text-xs whitespace-pre-line" x-html="info.value"></span>
                                    <template x-if="editingInfo?.id === info.id">
                                        <textarea class="w-full" x-model="editingInfo.value" rows="1"></textarea>
                                    </template>
                                </td>
                                @if(auth()->user()->role === 'superAdmin' || auth()->user()->role === 'admin')
                                    <td class="text-start border  p-1 w-full print:hidden">
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="btn btn-danger text-xs text-blue-500 font-bold" x-on:click="editInfo(info)" x-show="!editingInfo || editingInfo?.id !== info.id">{{ __('Edit') }}</button>
                                            <button type="button" class="btn btn-danger text-xs text-green-500 font-bold" x-on:click="confirmEditInfo(info)" x-show="editingInfo?.id === info.id">{{ __('Confirm') }}</button>
                                            <button type="button" class="btn btn-danger text-xs text-red-500 font-bold" x-on:click="cancelEditInfo(info)" x-show="editingInfo?.id === info.id">{{ __('Cancel') }}</button>
                                            <button type="button" class="btn btn-danger text-xs text-red-500 font-bold" x-on:click="deleteInfo(info)" x-show="!editingInfo || editingInfo?.id !== info.id">{{ __('Delete') }}</button>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        </template>
                    </template>
                </table>

                @if(auth()->user()->role === 'superAdmin' || auth()->user()->role === 'admin')
                    <form x-on:submit.prevent="submitInfo" class="flex items-center gap-3 print:hidden mt-3 w-full">
                        <textarea x-model="infoForm.key" rows="1" name="key" id="key" class="w-full"></textarea>
                        <textarea x-model="infoForm.value" rows="1" name="value" id="value" class="w-full"></textarea>
                        <button type="submit" class="btn btn-primary text-xs text-green-500 font-bold">{{ __('Add') }}</button>
                    </form>
                @endif



                <!-- actions table -->
                <h2 class="text-lg font-bold w-full text-start">{{ __('Actions') }}</h2>

                <form x-on:submit.prevent="submitAction" class="flex items-center gap-3 w-full">
                    <table class="w-full mb-10">

                        <thead>


                                
                            <tr>
                                <th class="text-start border  p-1 print:text-xs bg-primary print:bg-white print:text-black text-white">{{ __('Date') }}</th>
                                <th class="text-start border  p-1 print:text-xs bg-primary print:bg-white print:text-black text-white">{{ __('Authority') }}</th>
                                <th class="text-start border  p-1 print:text-xs bg-primary print:bg-white print:text-black text-white">{{ __('Description') }}</th>
                                <th class="text-start border  p-1 print:text-xs bg-primary print:bg-white print:text-black text-white">{{ __('Notes') }}</th>
                                <th class="text-start border  p-1 print:hidden bg-primary print:bg-white print:text-black text-white">{{ __('Creator') }}</th>
                                <th class="text-start border  p-1 print:hidden bg-primary print:bg-white print:text-black text-white"></th>
                            </tr>

                            <tr class="print:hidden">
                                <th class="border ">
                                    <div class="flex items-center">
                                        <input x-model="actionForm.date" class="h-[38px] w-full" type="date" name="date" id="date" class="w-full">
                                    </div>
                                </th>
                                <th class="border ">
                                    <div class="flex items-center">
                                        <textarea x-model="actionForm.authority" class="h-[38px] w-full" name="authority" id="authority" class="w-full" rows="1"></textarea>
                                    </div>
                                </th>
                                <th class="border ">
                                    <div class="flex items-center">
                                        <textarea x-model="actionForm.description" class="h-[38px] w-full" name="description" id="description" class="w-full" rows="1"></textarea>
                                    </div>
                                </th>
                                <th class="border ">
                                    <div class="flex items-center">
                                        <textarea x-model="actionForm.notes" class="h-[38px] w-full" name="notes" id="notes" class="w-full" rows="1"></textarea>
                                    </div>
                                </th>
                                <th class="border "></th>
                                <th class="border ">
                                    <button type="submit" class="btn btn-primary text-xs text-green-500 font-bold">{{ __('Add') }}</button>
                                </th>
                            </tr>
                                
                        </thead>
                            
                        <tbody>
                            <template x-if="actions.length > 0">
                                <template x-for="action in sortedActionsByDate">
                                    <tr class="odd:bg-zinc-100 even:bg-white">
                                        <td class="text-start border p-1 print:text-xs">
                                            <div class="flex items-center">
                                                <span x-show="!editingAction || editingAction?.id !== action.id" class="whitespace-nowrap" x-text="action.date"></span>
                                                <template x-if="editingAction?.id === action.id">
                                                    <input class="h-[38px] w-full" x-model="editingAction.date" type="date" class="w-full">
                                                </template>
                                            </div>
                                        </td>
                                        <td class="text-start border p-1 print:text-xs">
                                            <div class="flex items-center">
                                                <span x-show="!editingAction || editingAction?.id !== action.id" class="whitespace-pre-line" x-html="action.authority"></span>
                                                <template x-if="editingAction?.id === action.id">
                                                    <textarea class="h-[38px] w-full" x-model="editingAction.authority" rows="1"></textarea>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="text-start border p-1 print:text-xs">
                                            <div class="flex items-center">
                                                <span x-show="!editingAction || editingAction?.id !== action.id" class="whitespace-pre-line" x-html="action.description"></span>
                                                <template x-if="editingAction?.id === action.id">
                                                    <textarea class="h-[38px] w-full" x-model="editingAction.description" rows="1"></textarea>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="text-start border p-1 print:text-xs">
                                            <div class="flex items-center">
                                                <span x-show="!editingAction || editingAction?.id !== action.id" class="whitespace-pre-line" x-html="action.notes"></span>
                                                <template x-if="editingAction?.id === action.id">
                                                    <textarea class="h-[38px] w-full" x-model="editingAction.notes" rows="1"></textarea>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="text-start border p-1 text-xs print:hidden" x-text="action.creator.name"></td>
                                        <td class="text-start border p-1 print:hidden">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="editAction(action)" x-show="(!editingAction || editingAction?.id !== action.id) && action.can_edit" type="button" class="btn btn-primary text-xs text-blue-500 font-bold">{{ __('Edit') }}</button>
                                                <button @click="confirmEditAction(action)" x-show="editingAction?.id === action.id" type="button" class="btn btn-primary text-xs text-green-500 font-bold">{{ __('Confirm') }}</button>
                                                <button @click="cancelEditAction(action)" x-show="editingAction?.id === action.id" type="button" class="btn btn-primary text-xs text-red-500 font-bold">{{ __('Cancel') }}</button>
                                                <button @click="deleteAction(action)" x-show="(!editingAction || editingAction?.id !== action.id) && action.can_delete" type="button" class="btn btn-danger text-xs text-red-500 font-bold">{{ __('Delete') }}</button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                            
                    </table>
                </form>

            </div>
        </template>



    </div>

    <script>
        function reportComponent() {
            return {
                route: 'reports',
                filters: {
                    search: '',
                },

                selectedRecord: null,
                editingInfo: null,
                editingAction: null,

                showModal: false,
                records: [],
                infos: [],
                actions: [],
                currentPage: 1,
                totalPages: 1,
                totalResults: 0,

                infoForm: {
                    key: '',
                    value: '',
                },

                actionForm: {
                    date: '',
                    authority: '',
                    description: '',
                    notes: '',
                },

                submitAction() {
                    axios.post(`/actions/${this.selectedRecord?.id}`, this.actionForm)
                        .then((response) => {
                            this.actions.push(response.data);
                            this.actionForm.date = '';
                            this.actionForm.authority = '';
                            this.actionForm.description = '';
                            this.actionForm.notes = '';
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                editAction(action) {
                    this.editingAction = { ...action };
                },

                confirmEditAction(action) {
                    axios.put(`/actions/${action.id}`, this.editingAction)
                        .then((response) => {
                            this.actions = this.actions.map(a => a.id === response.data.id ? response.data : a);
                            this.editingAction = null;
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                cancelEditAction(action) {
                    this.editingAction = null;
                },

                deleteAction(action) {
                    if(!confirm('are you sure you want to delete this?')) {
                        return;
                    }
                    axios.delete(`/actions/${action.id}`)
                        .then((response) => {
                            this.actions = this.actions.filter(a => a.id !== action.id);
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                submitInfo() {
                    axios.post(`/infos/${this.selectedRecord?.id}`, this.infoForm)
                        .then((response) => {
                            this.infos.push(response.data);
                            this.infoForm.key = '';
                            this.infoForm.value = '';
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                deleteInfo(info) {
                    if(!confirm('are you sure you want to delete this?')) {
                        return;
                    }
                    axios.delete(`/infos/${info.id}`)
                        .then((response) => {
                            this.infos = this.infos.filter(i => i.id !== info.id);
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                editInfo(info) {
                    this.editingInfo = { ...info };
                },

                confirmEditInfo(info) {
                    axios.put(`/infos/${info.id}`, {
                        key: this.editingInfo.key,
                        value: this.editingInfo.value,
                    })
                        .then((response) => {
                            this.infos = this.infos.map(i => i.id === response.data.id ? response.data : i);
                            this.editingInfo = null;
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                get sortedActionsByDate() {
                    return this.actions?.sort((a, b) => new Date(a.date) - new Date(b.date)) ?? [];
                },

                cancelEditInfo(info) {
                    this.editingInfo = null;
                },

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

                setSelectedRecord(record) {
                    this.selectedRecord = record;
                    this.editingInfo = null;
                    this.editingAction = null;
                    this.fetchInfos();
                    this.fetchActions();
                },

                fetchInfos() {
                    this.infos = [];
                    axios.get(`/infos/${this.selectedRecord?.id}`)
                        .then((response) => {
                            this.infos = response.data;
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                fetchActions() {
                    this.actions = [];
                    axios.get(`/actions/${this.selectedRecord?.id}`)
                        .then((response) => {
                            this.actions = response.data;
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                get hasFilters() {
                    return Object.values(this.filters).some(filter => filter?.length > 0);
                },

                resetFilters() {
                    Object.keys(this.filters).forEach(key => this.filters[key] = []);
                },

                fetchRecords(page = 1) {
                    axios.get(`/${this.route}`, {
                            params: {
                                page: page,
                                filters: this.filters ?? false,
                            }
                        })
                        .then((response) => {
                            const data = response.data;

                            if (page === 1) {
                                this.records = data;
                            } else {
                                this.records = [...this.records, ...data];
                            }
                        })
                        .catch((error) => {
                            console.log(error.response.data.message);
                        });
                },

                loadMore() {
                    if (this.currentPage < this.totalPages) {
                        this.fetchRecords(this.currentPage + 1);
                    }
                },


            };
        }
    </script>
</x-app-layout>
