<x-app-layout title="Meja-O | Tutorial & Guidelines">
    @php
        $canAdminGuide = Auth::user()->hasPermission('tut_admin_guide');
        $canStaffGuide = Auth::user()->hasPermission('tut_staff_guide');
        
        $defaultTab = 'staff';
        if ($canAdminGuide && $canStaffGuide) {
            $defaultTab = (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) ? 'admin' : 'staff';
        } elseif ($canAdminGuide) {
            $defaultTab = 'admin';
        } elseif ($canStaffGuide) {
            $defaultTab = 'staff';
        } else {
            $defaultTab = 'none';
        }
    @endphp

    <main class="w-full max-w-[76rem] mx-auto px-3 sm:px-6 py-6 sm:py-8 space-y-6"
        x-data="{ 
            userRole: '{{ Auth::user()->role }}',
            selectedRole: '{{ $defaultTab }}',
            canAdminGuide: {{ $canAdminGuide ? 'true' : 'false' }},
            canStaffGuide: {{ $canStaffGuide ? 'true' : 'false' }},
            staffStep: 1,
            adminStep: 1
        }">

        <!-- Page Header & Mode Switcher -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Tutorial & Operational Guidelines') }}</h1>
                    <span class="px-2.5 py-0.5 text-xs font-bold uppercase rounded-full border {{ Auth::user()->isSuperAdmin() ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : (Auth::user()->isAdmin() ? 'bg-slate-900 text-white border-slate-900' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                        {{ str_replace('_', ' ', Auth::user()->role) }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">
                    {{ __('Comprehensive step-by-step documentation for everyday store frontline operations and administrative management.') }}
                </p>
            </div>

            <!-- Role View Switcher (Only visible when user has permission for both guides) -->
            @if ($canAdminGuide && $canStaffGuide)
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl border border-slate-200/80 self-start md:self-auto shrink-0 text-xs font-bold">
                    <button type="button" @click="selectedRole = 'admin'"
                        :class="selectedRole === 'admin' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>{{ __('Admin Guide') }}</span>
                    </button>
                    <button type="button" @click="selectedRole = 'staff'"
                        :class="selectedRole === 'staff' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ __('Staff Guide') }}</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- ========================================================================= -->
        <!-- STAFF OPERATIONAL TUTORIAL -->
        <!-- ========================================================================= -->
        <div x-show="canStaffGuide && selectedRole === 'staff'" x-transition class="space-y-6">

            <!-- Banner Card -->
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-soft-xs space-y-2">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-[11px] font-black uppercase tracking-wider">
                    {{ __('Staff Frontline Guide') }}
                </div>
                <h2 class="text-xl sm:text-2xl font-black">{{ __('Floor Plan & Table Operations Workflow') }}</h2>
                <p class="text-xs sm:text-sm text-amber-100 max-w-2xl font-medium leading-relaxed">
                    {{ __('As store staff, your primary responsibility is serving guests smoothly: finding open tables on the live grid, seating arriving parties, updating guest counts, and releasing cleared tables.') }}
                </p>
            </div>

            <!-- Step 1: Dashboard & Live Grid -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">1</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Reading Table Status on the Dashboard Grid') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Instant color indicators reflect live physical table occupancy.') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-emerald-200/80 bg-emerald-50/40 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <h4 class="font-bold text-xs sm:text-sm text-emerald-900">{{ __('Green Tables — Available') }}</h4>
                        </div>
                        <p class="text-xs text-emerald-800 leading-relaxed">
                            {{ __('Tables rendered in green are currently available (empty) and ready for immediate seating. Click on any green table to begin seating arriving guests.') }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-rose-200/80 bg-rose-50/40 space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-rose-500 shrink-0"></span>
                            <h4 class="font-bold text-xs sm:text-sm text-rose-900">{{ __('Red Tables — Occupied (Active Dining)') }}</h4>
                        </div>
                        <p class="text-xs text-rose-800 leading-relaxed">
                            {{ __('Tables turn red when diners are seated. The red card displays the seated Pax (guest count) and a live timer tracking how long the party has been dining.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 2: Seating Guests -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">2</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('How to Seat Arriving Guests') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Seating walk-in parties directly from the floor plan.') }}</p>
                    </div>
                </div>

                <ol class="space-y-3 text-xs sm:text-sm text-slate-600">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">A</span>
                        <div>
                            <strong class="text-slate-800 font-semibold">{{ __('Click an Available Table:') }}</strong>
                            <span>{{ __('On the floor grid, click the target table. A seating modal opens displaying the table name and capacity.') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">B</span>
                        <div>
                            <strong class="text-slate-800 font-semibold">{{ __('Enter Number of Guests (Pax):') }}</strong>
                            <span>{{ __('Type or select how many people are sitting at this table.') }}</span>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">C</span>
                        <div>
                            <strong class="text-slate-800 font-semibold">{{ __('Confirm Seating:') }}</strong>
                            <span>{{ __('Click "Seat Guests". The table instantly turns red (Occupied) and begins tracking dining time.') }}</span>
                        </div>
                    </li>
                </ol>
            </div>

            <!-- Step 3: During Dining & Checkout -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">3</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Updating Seated Parties & Clearing Tables') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Managing active diners and completing visits.') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs sm:text-sm">
                    <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-2">
                        <h4 class="font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            {{ __('Adjust Guest Count (Pax)') }}
                        </h4>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('If additional friends join a seated table, click the red (Occupied) table, update the total Pax, and click Save. The timer continues running uninterrupted.') }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/50 space-y-2">
                        <h4 class="font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ __('Checkout / Clear Table') }}
                        </h4>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('Once diners leave and the table has been cleaned, click the red (Occupied) table and choose "Checkout / Finish". The table reverts back to green (Available), and session duration is automatically recorded for store analytics.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Step 4: Waitlist Management -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">4</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Waitlist Queue Operations') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Managing waiting customers during rush hours.') }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">+</span>
                        <div>
                            <strong class="text-slate-800 font-semibold">{{ __('Adding to Waitlist:') }}</strong>
                            <span>{{ __('Open the Waitlist menu. Click "Add to Waitlist", enter customer name, phone number, and Pax size. The queue orders automatically by arrival time.') }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">&check;</span>
                        <div>
                            <strong class="text-slate-800 font-semibold">{{ __('Seating from Waitlist:') }}</strong>
                            <span>{{ __('When a table opens up, click "Seat" next to the waiting party, choose the table number, and confirm. They are instantly transferred to the dashboard!') }}</span>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-800 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">&times;</span>
                        <div>
                            <strong class="text-slate-800 font-semibold">{{ __('Customer Cancellation:') }}</strong>
                            <span>{{ __('If a waiting party decides to leave, click "Cancel" to remove them from the active queue.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Profile & Password Help -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">5</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Your Profile & Password Assistance') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Viewing your details and requesting help.') }}</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs sm:text-sm text-slate-600">
                    <p>{{ __('In the') }} <strong class="text-slate-800">{{ __('Profile') }}</strong> {{ __('tab, you can review your registered name, email, and assigned outlet.') }}</p>
                    <p>{{ __('To change your password, click') }} <strong class="text-slate-800">{{ __('Change Password') }}</strong> {{ __('and enter your current password for verification.') }}</p>
                    <p>{{ __('If you forget your password, click "Forgot Password" on the Sign In page. Your store Administrator or Super Admin will receive a notification badge on their Profile tab and can quickly assist you with your password.') }}</p>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- ADMIN & SUPER ADMIN MANAGEMENT TUTORIAL -->
        <!-- ========================================================================= -->
        <div x-show="canAdminGuide && selectedRole === 'admin'" x-transition class="space-y-6">

            <!-- Banner Card -->
            <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-6 text-white shadow-soft-xs space-y-2">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-[11px] font-black uppercase tracking-wider">
                    {{ __('Store Administration Guide') }}
                </div>
                <h2 class="text-xl sm:text-2xl font-black">{{ __('Managerial Controls & System Oversight') }}</h2>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl font-medium leading-relaxed">
                    {{ __('As an Administrator or Super Admin, you hold authority over table layout customizations, staff account approvals, password resets, access permissions, and analytics reporting.') }}
                </p>
            </div>

            <!-- Admin Step 1: Floor Plan Builder & Edit Modes -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">1</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Customizing the Floor Plan (Dashboard Builder)') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Configuring tables, colored zones, and room boundaries.') }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600">
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <strong class="text-slate-800 font-semibold">{{ __('Creating Tables & Sections:') }}</strong>
                        <p class="mt-1 text-slate-600">{{ __('Use the left inventory panel on the Dashboard to create new tables (with specific shapes & capacities) or colored sections (smoking area, VIP zone, outdoor patio). Drag and drop them onto the grid canvas.') }}</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <strong class="text-slate-800 font-semibold">{{ __('EDIT MODE Toggle (Tables vs Sections):') }}</strong>
                        <p class="mt-1 text-slate-600">{{ __('Toggle between "Tables" and "Sections" modes. When in Table edit mode, sections are locked in the background to prevent accidental drag/resizing, and vice versa.') }}</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                        <strong class="text-slate-800 font-semibold">{{ __('Canvas Dimensions & Resizing:') }}</strong>
                        <p class="mt-1 text-slate-600">{{ __('Click "Canvas Dimensions" above the grid to expand columns and rows for larger dining areas. Hover over the corner of any colored section to resize it.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin Step 2: Account Approval Menu -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">2</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Staff Account Approvals (Approval Menu)') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Vetting and admitting newly registered staff accounts.') }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600">
                    <p>{{ __('When a staff member creates an account via the Register page, their account is placed into a') }} <strong class="text-slate-800">{{ __('Pending Waiting Room') }}</strong> {{ __('and cannot access the dashboard until reviewed.') }}</p>
                    <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-900">
                        <strong class="font-bold flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center bg-amber-500 text-white text-[10px] font-extrabold aspect-square">1</span>
                            {{ __('Approval Notification Badge:') }}
                        </strong>
                        <p class="mt-1 text-xs text-amber-800">{{ __('Whenever there are pending staff registrations waiting for approval, a circular notification counter badge appears on the Approval navigation button.') }}</p>
                    </div>
                    <p>{{ __('Open the') }} <strong class="text-slate-800">{{ __('Approval') }}</strong> {{ __('menu, review the applicant name, outlet, and submission time, then click') }} <strong class="text-emerald-600">{{ __('Approve') }}</strong> {{ __('or') }} <strong class="text-rose-600">{{ __('Reject') }}</strong>.</p>
                </div>
            </div>

            <!-- Admin Step 3: Password Recovery Requests -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">3</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Managing Password Recovery Requests (Profile Tab)') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Resetting or viewing credentials for staff who forgot their password.') }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600">
                    <p>{{ __('When a staff member submits a password recovery request, a circular notification badge appears on your') }} <strong class="text-slate-800">{{ __('Profile') }}</strong> {{ __('menu button.') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                            <strong class="text-slate-800 font-bold">{{ __('Viewing Passwords:') }}</strong>
                            <p class="text-slate-600">{{ __('Admins can click the eye icon in the table to view the user current readable password and tell them verbally.') }}</p>
                        </div>
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                            <strong class="text-slate-800 font-bold">{{ __('Changing Passwords & Resolving:') }}</strong>
                            <p class="text-slate-600">{{ __('Click "Change Password" to assign a new password for the staff member, or click the green checkmark button to mark the request as resolved.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Step 4: Role & User Permissions -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">4</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Granular User Permissions & Templates') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Controlling feature access per role or per individual staff member.') }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs sm:text-sm text-slate-600">
                    <p>{{ __('In the') }} <strong class="text-slate-800">{{ __('Role & User Permission') }}</strong> {{ __('menu, you can manage access levels:') }}</p>
                    <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                        <li><strong class="text-slate-800">{{ __('Base Default Templates:') }}</strong> {{ __('Defines the baseline permissions that newly created or self-registered staff/admins inherit automatically.') }}</li>
                        <li><strong class="text-slate-800">{{ __('Bulk Overwrite:') }}</strong> {{ __('Instantly applies a standardized permission set across all existing store accounts at once.') }}</li>
                        <li><strong class="text-slate-800">{{ __('Individual User Exemptions:') }}</strong> {{ __('Search for any specific staff member by email to grant custom permissions. Exemptions differing from the default are marked with an orange indicator (!).') }}</li>
                    </ul>
                </div>
            </div>

            <!-- Admin Step 5: Database Reports & Analytics -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-soft-xs space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900 text-white font-black text-sm flex items-center justify-center shrink-0">5</div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">{{ __('Reports, Analytics & Peak Hour Habits') }}</h3>
                        <p class="text-xs text-slate-500 font-medium">{{ __('Data-driven insights to optimize table turnover and staffing.') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                        <strong class="text-slate-800 font-bold">{{ __('Overall Summary:') }}</strong>
                        <p class="text-slate-600">{{ __('Total visitor counts, average dining duration, and table turnover speed across all periods.') }}</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                        <strong class="text-slate-800 font-bold">{{ __('Periodic Trends:') }}</strong>
                        <p class="text-slate-600">{{ __('Compare daily, weekly, monthly, and yearly guest volumes to identify busy days.') }}</p>
                    </div>
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                        <strong class="text-slate-800 font-bold">{{ __('Customer Habits:') }}</strong>
                        <p class="text-slate-600">{{ __('Heatmaps and charts showing peak arrival hours for smarter staff scheduling.') }}</p>
                    </div>
                </div>
            </div>

        </div>

        @if (!$canAdminGuide && !$canStaffGuide)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-10 text-center text-slate-500 space-y-2">
                <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="font-bold text-slate-700">{{ __('No tutorial guides assigned.') }}</p>
                <p class="text-xs">{{ __('Please contact your store administrator to enable guide permissions for your account.') }}</p>
            </div>
        @endif

    </main>
</x-app-layout>
