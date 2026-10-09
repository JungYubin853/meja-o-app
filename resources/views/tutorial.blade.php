<x-app-layout title="Meja-O | Tut{{ __('or') }}ial & Guidelines">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 pb-20">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tut{{ __('or') }}ial & Guidelines</h1>
            <p class="text-slate-500 mt-2 text-sm sm:text-base">Welcome to the My Kopi-O Table Management System. Below is a comprehensive guide to help Staff and Admins navigate and utilize all the features effectively.</p>
        </div>

        <div class="space-y-6" x-data="{ activeTab: 'dashboard' }">
            
            <!-- Navigation Tabs -->
            <div class="flex overflow-x-auto gap-2 pb-2 hide-scrollbar">
                <button @click="activeTab = 'dashboard'" :class="activeTab === 'dashboard' ? 'bg-amber-500 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 b{{ __('or') }}der b{{ __('or') }}der-slate-200'" class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-col{{ __('or') }}s duration-200">
                    Flo{{ __('or') }} Plan & Tables
                </button>
                <button @click="activeTab = 'live'" :class="activeTab === 'live' ? 'bg-emerald-500 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 b{{ __('or') }}der b{{ __('or') }}der-slate-200'" class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-col{{ __('or') }}s duration-200">
                    Live Operations
                </button>
                <button @click="activeTab = 'waitlist'" :class="activeTab === 'waitlist' ? 'bg-sky-500 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 b{{ __('or') }}der b{{ __('or') }}der-slate-200'" class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-col{{ __('or') }}s duration-200">
                    Waitlist
                </button>
                <button @click="activeTab = 'admin'" :class="activeTab === 'admin' ? 'bg-indigo-500 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-50 b{{ __('or') }}der b{{ __('or') }}der-slate-200'" class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap transition-col{{ __('or') }}s duration-200">
                    Admin & Rep{{ __('or') }}ts
                </button>
            </div>

            <!-- Tab Content: Dashboard & Flo{{ __('or') }} Plan -->
            <div x-show="activeTab === 'dashboard'" x-transition.opacity.duration.300ms class="space-y-4">
                
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl b{{ __('or') }}der b{{ __('or') }}der-slate-100">
                    <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentCol{{ __('or') }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"></path></svg>
                        </span>
                        Interactive Flo{{ __('or') }} Plan Builder
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="space-y-3">
                            <h3 class="font-bold text-slate-800 text-lg">{{ __('1. Placing Tables & Sections') }}</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">{{ __('To add tables {{ __('or') }} col{{ __('or') }}ed sections to your flo{{ __('or') }} plan, you must first create them in the left-hand invent{{ __('or') }}y panel using the') }} <strong>Create New Table</strong> {{ __('or') }} <strong>{{ __('Create New Section') }}</strong> {{ __('buttons. Once created, simply') }} <strong>{{ __('Drag and Drop') }}</strong> {{ __('them directly onto the grid on the right side.') }}</p>
                        </div>
                        <div class="space-y-3">
                            <h3 class="font-bold text-slate-800 text-lg">2. Edit Modes</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">Use the <strong>EDIT MODE</strong> toggle on the left panel to switch between editing Tables {{ __('or') }} Sections. You can only drag, resize, {{ __('or') }} delete items that match your active Edit Mode. The inactive items will become blurred and unclickable.</p>
                        </div>
                        <div class="space-y-3">
                            <h3 class="font-bold text-slate-800 text-lg">3. Resizing & Moving</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">Once placed on the grid, you can move items by dragging them to a new square. To resize a col{{ __('or') }}ed section, hover over its bottom-right c{{ __('or') }}ner until you see the circular handle, click, and drag it to expand {{ __('or') }} shrink the section.</p>
                        </div>
                        <div class="space-y-3">
                            <h3 class="font-bold text-slate-800 text-lg">4. Canvas Dimensions</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">Need a bigger room? Click the <strong>Canvas Dimensions</strong> button above the grid to increase the number of columns and rows. You cannot shrink the grid if there are tables {{ __('or') }} sections currently occupying the outermost edges.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Live Operations -->
            <div x-show="activeTab === 'live'" x-transition.opacity.duration.300ms class="space-y-4" style="display: none;">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl b{{ __('or') }}der b{{ __('or') }}der-slate-100">
                    <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentCol{{ __('or') }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </span>
                        Live Table Operations
                    </h2>
                    
                    <div class="space-y-6 mt-6">
                        <div class="bg-slate-50 rounded-2xl p-5 b{{ __('or') }}der b{{ __('or') }}der-slate-200">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div> Seating a Customer
                            </h3>
                            <p class="text-slate-600 text-sm mt-2">When a customer arrives, click on an <strong>{{ __('Available') }}</strong> (white) table on the dashboard grid. A modal will appear allowing you to input the number of guests (Pax). Once confirmed, the table will turn green (Occupied).</p>
                        </div>
                        
                        <div class="bg-slate-50 rounded-2xl p-5 b{{ __('or') }}der b{{ __('or') }}der-slate-200">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-rose-500"></div> Finishing a Meal
                            </h3>
                            <p class="text-slate-600 text-sm mt-2">When the guests have finished their meal and the table is cleared, click on the <strong>{{ __('Occupied') }}</strong> (green) table. Confirm the completion prompt, and the table will revert to Available, automatically logging the session in the database f{{ __('or') }} your daily rep{{ __('or') }}ts.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content: Waitlist -->
            <div x-show="activeTab === 'waitlist'" x-transition.opacity.duration.300ms class="space-y-4" style="display: none;">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl b{{ __('or') }}der b{{ __('or') }}der-slate-100">
                    <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentCol{{ __('or') }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        Managing the Waitlist
                    </h2>
                    
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">The Waitlist page helps you manage customer queues during peak hours. It provides a real-time list of waiting parties, {{ __('or') }}dered automatically by arrival time.</p>
                    
                    <ul class="space-y-4">
                        <li class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0 mt-0.5">1</div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Adding Customers</h4>
                                <p class="text-slate-500 text-sm mt-1">Use the "Add to Waitlist" button to log a new party. You'll need their Name, Phone Number (optional), and the number of Pax.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0 mt-0.5">2</div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Seating from Waitlist</h4>
                                <p class="text-slate-500 text-sm mt-1">When a table becomes available, find the customer in the waitlist and click "Seat". You will be prompted to select which specific table number they are being seated at.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0 mt-0.5">3</div>
                            <div>
                                <h4 class="font-bold text-slate-800 text-sm">Cancellations</h4>
                                <p class="text-slate-500 text-sm mt-1">If a party leaves bef{{ __('or') }}e being seated, click "Cancel" to remove them from the active queue. They will still be logged as cancelled in the database f{{ __('or') }} analytics.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tab Content: Admin -->
            <div x-show="activeTab === 'admin'" x-transition.opacity.duration.300ms class="space-y-4" style="display: none;">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl b{{ __('or') }}der b{{ __('or') }}der-slate-100">
                    <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentCol{{ __('or') }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </span>
                        Admin & Rep{{ __('or') }}ting
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-slate-50 rounded-2xl p-5 b{{ __('or') }}der b{{ __('or') }}der-slate-200">
                            <h3 class="font-bold text-slate-800 mb-2">Rep{{ __('or') }}ts & Database</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">This page provides hist{{ __('or') }}ical data of all table sessions and waitlist logs. You can filter the data by date range, search by specific table numbers {{ __('or') }} customer names, and exp{{ __('or') }}t the logs to CSV f{{ __('or') }}mat f{{ __('or') }} external auditing {{ __('or') }} management reviews.</p>
                        </div>
                        <div class="bg-slate-50 rounded-2xl p-5 b{{ __('or') }}der b{{ __('or') }}der-slate-200">
                            <h3 class="font-bold text-slate-800 mb-2">Role & User Permission</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">Exclusively f{{ __('or') }} Admins. This p{{ __('or') }}tal allows you to fine-tune what each staff member can see and do. You can restrict access to certain pages (like Rep{{ __('or') }}ts) {{ __('or') }} specific actions (like Deleting Tables {{ __('or') }} Accessing Account Management).</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</x-app-layout>
