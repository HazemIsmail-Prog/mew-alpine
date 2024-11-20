<div x-data="page()" class="flex h-full">

    {{-- List --}}
    <div class="flex-1 mx-auto overflow-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            {{-- Create New Buttons --}}
            <div class="flex items-center gap-3 my-3">

                <button x-on:click="newUser()" type="button"
                    class="flex items-center justify-center gap-1 h-9 py-1 w-28 rounded-lg text-white font-bold !bg-danger px-4">
                    <span aria-hidden="true" class="material-design-icon tray-arrow-up-icon" role="img">
                        <svg fill="currentColor" class="material-design-icon__svg" width="20" height="20"
                            viewBox="0 0 24 24">
                            <path
                                d="M2 12H4V17H20V12H22V17C22 18.11 21.11 19 20 19H4C2.9 19 2 18.11 2 17V12M12 2L6.46 7.46L7.88 8.88L11 5.75V15H13V5.75L16.13 8.88L17.55 7.45L12 2Z">
                                <!---->
                            </path>
                        </svg>
                    </span>
                    <span class="hidden md:block">
                        {{ __('New') }}
                    </span>
                </button>
            </div>

            <div class="p-2 text-gray-900 flex flex-col gap-2">
                @foreach ($this->users as $user)
                    @include('components.user-row')
                @endforeach
            </div>
        </div>
    </div>
    @include('components.user-form-section')
</div>

<script>
    function page() {
        return {

            // ################################### Globals ###############################################

            contracts: @json($this->contracts),
            stakeholders: @json($this->stakeholders),
            types: @json($this->types),

            init() {
                Livewire.on('userCreated', user => {
                    this.$nextTick(() => {
                        this.resetUserForm()
                        this.openUserForm(user[0]);
                    });
                });
                Livewire.on('userDeleted', () => {
                    this.$nextTick(() => {
                        this.closeUserFormModal()
                    });
                });
            },

            getName(type, id) {

                item = null

                switch (type) {
                    case 'type':
                        item = this.typesList.find(e => e.id === id)
                        break;
                    case 'status':
                        item = this.statusesList.find(e => e.id === id)
                        break;
                    case 'user':
                        item = this.users.find(e => e.id === id)
                        break;
                    case 'contract':
                        item = this.contracts.find(e => e.id === id)
                        break;
                    case 'stakeholder':
                        item = this.stakeholders.find(e => e.id === id)
                        break;
                }

                return item ? item.name : "---";
            },


            // ################################### Globals ###############################################

            // ################################### User ###############################################

            showUserForm: false,
            currentSelectedUserId: null,
            userForm: @entangle('userForm'),

            userFormTemplate: {
                id: null,
                name: '',
                username: '',
                password: '',
                type: 'user',
                is_active: true,
                stakeholder_id: null,
                contract_ids: []
            },

            resetUserForm() {
                this.userForm = JSON.parse(JSON.stringify(this.userFormTemplate));
            },

            openUserForm(user = null) {
                if (this.userForm.id == user.id) {
                    this.closeUserFormModal();
                } else {
                    // this.resetUserForm();
                    this.showUserForm = true;
                    this.userForm.id = user.id ?? null;
                    this.userForm.name = user.name ?? '';
                    this.userForm.type = user.type ?? '';
                    this.userForm.username = user.username ?? '';
                    this.userForm.password = ''; // Password should not be pre-filled for security reasons
                    this.userForm.is_active = user.is_active ?? false;
                    this.userForm.stakeholder_id = user.stakeholder_id ?? null;
                    this.userForm.contract_ids = user.contract_ids ?? [];
                }
            },

            closeUserFormModal() {
                this.showUserForm = false;
                this.resetUserForm();
            },

            toggleUserIsActive(user, newValue) {
                @this.toggleUserIsActive(user.id, newValue)
            },

            submitUser() {
                @this.saveUser();
            },

            newUser() {
                this.closeUserFormModal();
                this.showUserForm = true;
            },

            // ################################### User ###############################################
        }
    }
</script>
