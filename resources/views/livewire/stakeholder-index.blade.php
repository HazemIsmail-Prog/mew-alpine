<div x-data="page()" class="flex h-full">

    {{-- List --}}
    <div class="flex-1 mx-auto overflow-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            {{-- Create New Buttons --}}
            <div class="flex items-center gap-3 my-3">

                <button x-on:click="newStakeholder()" type="button"
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
                @foreach ($this->stakeholders as $stakeholder)
                    @include('components.stakeholder-row')
                @endforeach
            </div>
        </div>
    </div>
    @include('components.stakeholder-form-section')
</div>

<script>
    function page() {
        return {

            // ################################### Globals ###############################################

            stakeholders: @json($this->stakeholders),

            init() {
                Livewire.on('stakeholderCreated', stakeholder => {
                    this.$nextTick(() => {
                        this.resetStakeholderForm()
                        this.openStakeholderForm(stakeholder[0]);
                    });
                });
                Livewire.on('stakeholderDeleted', () => {
                    this.$nextTick(() => {
                        this.closeStakeholderFormModal()
                    });
                });
            },


            // ################################### Globals ###############################################


            // ################################### Stakeholder ###############################################

            showStakeholderForm: false,
            currentSelectedStakeholderId: null,
            stakeholderForm: @entangle('stakeholderForm'),

            stakeholderFormTemplate: {
                id: null,
                name: '',
                is_active: true,
            },

            resetStakeholderForm() {
                this.stakeholderForm = JSON.parse(JSON.stringify(this.stakeholderFormTemplate));
            },

            openStakeholderForm(stakeholder = null) {
                if (this.stakeholderForm.id == stakeholder.id) {
                    this.closeStakeholderFormModal();
                } else {
                    // this.resetStakeholderForm();
                    this.showStakeholderForm = true;
                    this.stakeholderForm.id = stakeholder.id ?? null;
                    this.stakeholderForm.name = stakeholder.name ?? '';
                    this.stakeholderForm.is_active = stakeholder.is_active ?? false;
                }
            },

            closeStakeholderFormModal() {
                this.showStakeholderForm = false;
                this.resetStakeholderForm();
            },

            toggleStakeholderIsActive(stakeholder, newValue) {
                @this.toggleStakeholderIsActive(stakeholder.id, newValue)
            },

            submitStakeholder() {
                @this.saveStakeholder();
            },

            newStakeholder() {
                this.closeStakeholderFormModal();
                this.showStakeholderForm = true;
            },

            // ################################### Stakeholder ###############################################
        }
    }
</script>
