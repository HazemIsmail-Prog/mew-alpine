<x-app-layout>

    <x-slot name="title">{{__('Meetings')}}</x-slot>

    <div x-data="meetingsComponent()" class="flex flex-col lg:flex-row h-full">
        <div class="flex-1 mx-auto overflow-auto w-full">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2 sm:p-4 max-w-full">
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-5">{{__('Meetings Schedule')}}</h1>


                <!-- View Toggle Buttons and New Meeting Button -->
                <div class="flex items-center gap-2 justify-between w-full mb-3 print:hidden">
                    <div class="flex items-center gap-2">
                        <button
                            x-on:click="viewMode = 'timeline'"
                            type="button"
                            class="p-2 rounded-lg border touch-manipulation transition-colors"
                            :class="viewMode === 'timeline' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                            title="{{ __('Timeline') }}"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <button
                            x-on:click="viewMode = 'calendar'"
                            type="button"
                            class="p-2 rounded-lg border touch-manipulation transition-colors"
                            :class="viewMode === 'calendar' ? 'bg-primary text-white border-primary' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                            title="{{ __('Calendar') }}"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>

                    @can('create', \App\Models\Meeting::class)
                        <button 
                            x-on:click="openModal()"
                            type="button"
                            class="flex items-center justify-center gap-1 h-9 py-1 px-3 sm:px-4 rounded-lg text-white font-bold !bg-primary touch-manipulation"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="text-xs sm:text-sm">{{ __('New') }}</span>
                        </button>
                    @endcan
                </div>

                <!-- Header with Navigation and View Toggle -->
                <div class="flex items-center gap-2 sm:gap-3 w-fit mb-3 print:hidden">
                    <button 
                        x-on:click="previousMonth()" 
                        type="button"
                        class="p-2 sm:p-2 rounded-lg border border-gray-300 hover:bg-gray-100 active:bg-gray-200 touch-manipulation"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                    <h2 class="text-base font-bold text-gray-900 text-center w-[150px] flex-1" x-text="getMonthYear()"></h2>
                    <button 
                        x-on:click="nextMonth()" 
                        type="button"
                        class="p-2 sm:p-2 rounded-lg border border-gray-300 hover:bg-gray-100 active:bg-gray-200 touch-manipulation"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>



                <!-- Calendar View -->
                <div x-show="viewMode === 'calendar'" class="border border-gray-200 rounded-lg overflow-x-auto">
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
                                        <template x-for="meeting in getMeetingsForDay(day.fullDate)" :key="meeting.id">
                                            <div 
                                                x-on:mouseenter="hoveredMeetingId = meeting.id"
                                                x-on:mouseleave="hoveredMeetingId = null"
                                                class="text-[10px] sm:text-xs p-0.5 sm:p-1 rounded break-words whitespace-normal touch-manipulation active:opacity-75 leading-tight transition-colors duration-150 relative group"
                                                x-bind:class="{
                                                    'bg-blue-600 text-white': hoveredMeetingId !== meeting.id,
                                                    'bg-blue-800 text-white': hoveredMeetingId === meeting.id,
                                                }">
                                                <div x-text="meeting.formatted_start_time + (meeting.formatted_end_time ? ' - ' + meeting.formatted_end_time : '')"></div>
                                                <div x-text="meeting.title"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Timeline View -->
                <div x-show="viewMode === 'timeline'" class="overflow-hidden">
                    <template x-if="timelineDays.length === 0">
                        <div class="p-8 text-center">
                            <p class="text-gray-500 text-sm sm:text-base">{{ __('No meetings scheduled for this month') }}</p>
                        </div>
                    </template>
                    <div x-show="timelineDays.length > 0">
                        <template x-for="day in timelineDays" :key="day.date">
                            <div class="mb-8">
                                <div class="flex items-center w-full mb-3">
                                    <div class="flex-grow border-t border-gray-300"></div>
                                    <div class="flex items-center gap-2 px-3 whitespace-nowrap">
                                        <h3 class="text-sm sm:text-base font-semibold text-gray-900" x-text="day.formattedDate"></h3>
                                        <span class="inline-block px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold" x-text="day.meetings.length"></span>
                                    </div>
                                    <div class="flex-grow border-t border-gray-300"></div>
                                </div>
                                <div class="space-y-2">
                                    <template x-for="meeting in day.meetings" :key="meeting.id">
                                        <div class="space-y-2 bg-blue-50 border-dashed border border-blue-500 p-3 rounded-lg hover:bg-blue-100 transition-colors touch-manipulation break-inside-avoid">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"></path>
                                                </svg>
                                                <div class="text-xs sm:text-sm font-semibold text-blue-700" x-text="meeting.formatted_start_time + (meeting.formatted_end_time ? ' - ' + meeting.formatted_end_time : '')"></div>
                                            </div>
                                            <div class="text-sm sm:text-base font-bold text-gray-900" x-text="meeting.title"></div>
                                            
                                            <template x-if="meeting.contracts.length > 0">
                                                <div class="flex flex-col gap-1">
                                                    <template x-for="(contract, index) in meeting.contracts" :key="index">
                                                        <div class="flex items-center gap-1 text-xs text-primary">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3 shrink-0">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                            </svg>
                                                            <span x-text="contract.name"></span>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="meeting.location">
                                                <div class="flex items-center gap-1 text-xs text-gray-600 mb-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <span x-text="meeting.location"></span>
                                                </div>
                                            </template>
                                            <template x-if="meeting.attendees">
                                                <div class="bg-gray-50 border border-gray-200 rounded-md p-2 mt-1">
                                                    <div class="flex items-start gap-1.5 text-xs text-gray-700">
                                                        <svg class="w-3.5 h-3.5 mt-0.5 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                                        </svg>
                                                        <span class="break-words whitespace-pre-wrap font-medium" x-text="meeting.attendees"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            @if (auth()->user()->role === 'superAdmin' || Auth::user()->role === 'admin')
                                                <hr class="border-blue-500 border-dashed print:hidden">
                                                <div class="flex items-center justify-end gap-2 print:hidden">
                                                    <button 
                                                        x-on:click.stop="openModal(meeting)"
                                                        type="button"
                                                        class="p-1.5 rounded hover:bg-blue-200 active:bg-blue-300 transition-colors touch-manipulation"
                                                        title="{{ __('Edit Meeting') }}"
                                                    >
                                                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        x-on:click.stop="duplicateMeeting(meeting)"
                                                        type="button"
                                                        class="p-1.5 rounded hover:bg-blue-200 active:bg-blue-300 transition-colors touch-manipulation"
                                                        title="{{ __('Duplicate Meeting') }}"
                                                    >
                                                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        x-on:click.stop="deleteRecord(meeting)"
                                                        type="button"
                                                        class="p-1.5 rounded hover:bg-red-200 active:bg-red-300 transition-colors touch-manipulation"
                                                        title="{{ __('Delete Meeting') }}"
                                                    >
                                                        <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        @include('pages.meetings.form')
    </div>
</x-app-layout>

<script>
    function meetingsComponent() {
        return {
            route: 'meetings',
            showModal: false,
            records: [],
            form: [],
            currentPage: 1,
            totalPages: 1,
            totalResults: 0,
            loading: false,
            currentMonth: new Date().getMonth() + 1,
            currentYear: new Date().getFullYear(),
            calendarDays: [],
            timelineDays: [],
            viewMode: 'timeline',
            hoveredMeetingId: null,
            locations: @json($locations),

            init() {
                axios.defaults.headers.common["X-CSRF-TOKEN"] = document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content");
                this.fetchMeetings();
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
                console.log('buildCalendar');
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

            buildTimeline() {
                console.log('buildTimeline');
                const daysInMonth = new Date(this.currentYear, this.currentMonth, 0).getDate();
                const monthNames = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                    'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                const dayNames = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                
                this.timelineDays = [];
                
                for (let date = 1; date <= daysInMonth; date++) {
                    const fullDate = `${this.currentYear}-${String(this.currentMonth).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    const dayMeetings = this.getMeetingsForDay(fullDate);
                    
                    // Only add days that have meetings
                    if (dayMeetings.length > 0) {
                        const dayDate = new Date(this.currentYear, this.currentMonth - 1, date);
                        const dayName = dayNames[dayDate.getDay()];
                        
                        this.timelineDays.push({
                            date: fullDate,
                            formattedDate: `${dayName}، ${date} ${monthNames[this.currentMonth - 1]} ${this.currentYear}`,
                            meetings: dayMeetings
                        });
                    }
                }
            },

            getMeetingsForDay(dateStr) {
                return this.records.filter(meeting => {
                    // Compare date strings directly (format: YYYY-MM-DD)
                    return meeting.date === dateStr;
                });
            },


            getMonthYear() {
                const monthNames = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                    'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                return `${monthNames[this.currentMonth - 1]} ${this.currentYear}`;
            },

            previousMonth() {
                if (this.currentMonth === 1) {
                    this.currentMonth = 12;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.fetchMeetings();
            },

            nextMonth() {
                if (this.currentMonth === 12) {
                    this.currentMonth = 1;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.fetchMeetings();
            },

            // goToToday() {
            //     const today = new Date();
            //     this.currentMonth = today.getMonth() + 1;
            //     this.currentYear = today.getFullYear();
            //     this.buildCalendar();
            //     this.fetchMeetings();
            // },

            duplicateMeeting(meeting) {
                // Create a copy of the meeting without the ID
                const duplicatedMeeting = {
                    ...meeting,
                    id: null, // Remove ID so it's treated as a new meeting
                    contract_ids: meeting.contracts?.map(contract => contract.id) ?? []
                };
                this.openModal(duplicatedMeeting);
            },

            fillForm(record) {
                if (record) {
                    this.form = {
                        ...record,
                        contract_ids: record.contracts?.map(contract => contract.id) ?? []
                    };
                } else {
                    this.form = this.getEmptyForm();
                }
            },

            getEmptyForm() {
                return {
                    id: null,
                    title: '',
                    date: '',
                    start_time: '',
                    end_time: '',
                    location: '',
                    attendees: '',
                    notes: '',
                    agenda: '',
                    contract_ids: [],
                };
            },

            fetchMeetings() {
                axios.get(`/${this.route}`, {
                        params: {
                            month: this.currentMonth,
                            year: this.currentYear
                        }
                    })
                    .then((response) => {
                        this.records = response.data;
                        this.$nextTick(() => {
                            this.buildCalendar();
                            this.buildTimeline();
                        });
                    })
                    .catch((error) => console.error(error.response?.data?.message || error.message))
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
                        this.fetchMeetings();
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
                        this.fetchMeetings();
                    })
                    .catch(error => {
                        console.error(error.response?.data?.message || error.message);
                        alert('Failed to delete the record.');
                    });
            },
        };
    }
</script>
