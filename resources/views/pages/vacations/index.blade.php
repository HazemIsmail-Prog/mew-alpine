<x-app-layout>
    <div x-data="vacationsComponent()" class="flex flex-col lg:flex-row h-full">
        <div class="flex-1 mx-auto overflow-auto w-full">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2 sm:p-4 max-w-full">
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-5">{{__('Vacations')}}</h1>

                <!-- Calendar Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-3 sm:mb-4">
                    <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                        <button 
                            x-on:click="previousMonth()" 
                            type="button"
                            class="p-2 sm:p-2 rounded-lg border border-gray-300 hover:bg-gray-100 active:bg-gray-200 touch-manipulation"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <h2 class="text-base sm:text-xl font-bold text-gray-900 flex-1 text-center sm:text-left" x-text="getMonthYear()"></h2>
                        <button 
                            x-on:click="nextMonth()" 
                            type="button"
                            class="p-2 sm:p-2 rounded-lg border border-gray-300 hover:bg-gray-100 active:bg-gray-200 touch-manipulation"
                        >
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <button 
                            x-on:click="goToToday()" 
                            type="button"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg border border-gray-300 hover:bg-gray-100 active:bg-gray-200 text-xs sm:text-sm touch-manipulation"
                        >
                            {{ __('Today') }}
                        </button>
                    </div>

                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'superAdmin')
                        <button x-on:click="openModal()" type="button"
                            class="flex items-center justify-center gap-1 h-9 py-1 w-full sm:w-28 rounded-lg text-white font-bold !bg-danger px-4 touch-manipulation">
                            {{ __('New') }}
                        </button>
                    @endif
                </div>

                <!-- Calendar Grid -->
                <div class="border border-gray-200 rounded-lg overflow-x-auto">
                    <div class="min-w-[350px]">
                        <!-- Day Headers -->
                        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                            <template x-for="day in ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت']" :key="day">
                                <div class="p-1 sm:p-2 text-center text-[12px] sm:text-sm font-semibold text-gray-700 border-r border-gray-200 last:border-r-0">
                                    <span x-text="day"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Calendar Days -->
                        <div class="grid grid-cols-7">
                            <template x-for="(day, index) in calendarDays" :key="index">
                                <div class="min-h-[80px] sm:min-h-[120px] border-r border-b last:border-r-0 p-0.5 sm:p-1 transition-colors relative"
                                    x-bind:class="getDayCellClass(day).backgroundColor">
                                    <div class="flex items-center justify-between mb-0.5 sm:mb-1 relative z-20">
                                        <span class="text-xs sm:text-sm font-medium px-0.5 sm:px-1"
                                            x-bind:class="getDayCellClass(day).textColor" x-text="day.date"></span>
                                    </div>
                                    <div class="flex flex-col gap-0.5 sm:gap-1">
                                        <template x-for="vacation in getVacationsForDay(day.fullDate)" :key="vacation.id">
                                            <div x-on:click="vacation.can_update ? openModal(vacation) : null"
                                                x-on:mouseenter="hoveredVacationId = vacation.id"
                                                x-on:mouseleave="hoveredVacationId = null"
                                                class="text-[10px] sm:text-xs p-0.5 sm:p-1 rounded cursor-pointer break-words whitespace-normal touch-manipulation active:opacity-75 leading-tight transition-colors duration-150"
                                                x-bind:class="{
                                                    'bg-green-600 text-white': vacation.is_current && hoveredVacationId !== vacation.id,
                                                    'bg-green-700 text-white': vacation.is_current && hoveredVacationId === vacation.id,
                                                    'bg-gray-500 text-white': (vacation.is_future || vacation.is_past) && hoveredVacationId !== vacation.id,
                                                    'bg-gray-600 text-white': (vacation.is_future || vacation.is_past) && hoveredVacationId === vacation.id,
                                                }"
                                                x-text="getUserById(vacation.user_id)?.name || ''">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
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
            currentMonth: new Date().getMonth() + 1,
            currentYear: new Date().getFullYear(),
            calendarDays: [],
            hoveredVacationId: null,
            
            init() {
                axios.defaults.headers.common["X-CSRF-TOKEN"] = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");
                this.buildCalendar();
                this.fetchRecords();
            },

            getDayCellClass(day) {
                let backgroundColor = '';
                let textColor = '';
                if (day.isToday && day.isCurrentMonth) {
                    backgroundColor = 'bg-primary bg-opacity-20 border-primary border';
                    textColor = 'text-primary';
                } else if (day.isWeekend && day.isCurrentMonth) {
                    backgroundColor = 'bg-gray-200 border-gray-200';
                    textColor = 'text-gray-400';
                } else if (!day.isCurrentMonth) {
                    backgroundColor = 'bg-gray-200 border-gray-200';
                    textColor = 'text-gray-400';
                } else {
                    backgroundColor = 'bg-white border-gray-200';
                    textColor = 'text-gray-900';
                }
                return {
                    backgroundColor: backgroundColor,
                    textColor: textColor,
                };
            },

            buildCalendar() {
                const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
                const lastDay = new Date(this.currentYear, this.currentMonth, 0);
                const daysInMonth = lastDay.getDate();
                const startingDayOfWeek = firstDay.getDay();
                
                const today = new Date();
                const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                
                this.calendarDays = [];
                
                // Calculate previous month and year
                let prevMonth = this.currentMonth - 1;
                let prevYear = this.currentYear;
                if (prevMonth === 0) {
                    prevMonth = 12;
                    prevYear--;
                }
                const prevMonthLastDay = new Date(prevYear, prevMonth, 0);
                const daysInPrevMonth = prevMonthLastDay.getDate();
                
                // Add days from previous month
                for (let i = startingDayOfWeek - 1; i >= 0; i--) {
                    const date = daysInPrevMonth - i;
                    const fullDate = `${prevYear}-${String(prevMonth).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    this.calendarDays.push({
                        date: date,
                        fullDate: fullDate,
                        isCurrentMonth: false,
                        isToday: fullDate === todayStr,
                        isWeekend: new Date(fullDate).getDay() === 5 || new Date(fullDate).getDay() === 6,

                    });
                }
                
                // Add days from current month
                for (let date = 1; date <= daysInMonth; date++) {
                    const fullDate = `${this.currentYear}-${String(this.currentMonth).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    this.calendarDays.push({
                        date: date,
                        fullDate: fullDate,
                        isCurrentMonth: true,
                        isToday: fullDate === todayStr,
                        isWeekend: new Date(fullDate).getDay() === 5 || new Date(fullDate).getDay() === 6,
                    });
                }
                
                // Calculate next month and year
                let nextMonth = this.currentMonth + 1;
                let nextYear = this.currentYear;
                if (nextMonth === 13) {
                    nextMonth = 1;
                    nextYear++;
                }
                
                // Add days from next month to fill the grid
                const remainingDays = 42 - this.calendarDays.length; // 6 rows * 7 days
                for (let date = 1; date <= remainingDays; date++) {
                    const fullDate = `${nextYear}-${String(nextMonth).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    this.calendarDays.push({
                        date: date,
                        fullDate: fullDate,
                        isCurrentMonth: false,
                        isToday: fullDate === todayStr,
                        isWeekend: new Date(fullDate).getDay() === 5 || new Date(fullDate).getDay() === 6,
                    });
                }
            },

            getMonthYear() {
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
                return `${monthNames[this.currentMonth - 1]} ${this.currentYear}`;
            },

            previousMonth() {
                if (this.currentMonth === 1) {
                    this.currentMonth = 12;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.buildCalendar();
                this.fetchRecords();
            },

            nextMonth() {
                if (this.currentMonth === 12) {
                    this.currentMonth = 1;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.buildCalendar();
                this.fetchRecords();
            },

            goToToday() {
                const today = new Date();
                this.currentMonth = today.getMonth() + 1;
                this.currentYear = today.getFullYear();
                this.buildCalendar();
                this.fetchRecords();
            },

            getVacationsForDay(dateStr) {
                return this.records.filter(vacation => {
                    const startDate = new Date(vacation.start_date + 'T00:00:00');
                    const endDate = new Date(vacation.end_date + 'T23:59:59');
                    const dayDate = new Date(dateStr + 'T00:00:00');
                    
                    return dayDate >= startDate && dayDate <= endDate;
                });
            },

            resetFilters() {
                Object.keys(this.filters).forEach(key => this.filters[key] = []);
                this.fetchRecords();
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
                return this.users.find(user => user.id === id) || {};
            },

            fetchRecords() {
                if (this.loading) return;
                this.loading = true;
                axios.get(`/${this.route}`, {
                        params: {
                            month: this.currentMonth,
                            year: this.currentYear
                        }
                    })
                    .then((response) => {
                        this.records = response.data;
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
                        this.fetchRecords();
                        this.closeModal();
                    })
                    .catch((error) => {
                        console.error(error.response?.data?.message || error.message);
                        alert('Failed to save record. Please check your input.');
                    });
            },

            deleteRecord(record) {
                if (!confirm('Are you sure you want to delete this record?')) return;

                axios.delete(`/${this.route}/${record.id}`)
                    .then(() => {
                        this.records = this.records.filter(r => r.id !== record.id);
                        this.fetchRecords();
                    })
                    .catch(error => {
                        console.error(error.response?.data?.message || error.message);
                        alert('Failed to delete the record.');
                    });
            },
        };
    }
</script>
