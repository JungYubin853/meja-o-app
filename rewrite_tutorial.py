file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\tutorial.blade.php"

new_html = """<x-app-layout>
    <div class="min-h-screen bg-slate-50/50 pb-20">
        
        <main class="max-w-[64rem] mx-auto px-4 sm:px-6 py-8 sm:py-12" x-data="{ activeTab: 'dashboard' }">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ __('Tutorial & Guidelines') }}</h1>
                <p class="text-slate-500 mt-2 text-sm sm:text-base">{{ __('Welcome to the My Kopi-O Table Management System. Below is a comprehensive guide to help Staff and Admins navigate and utilize all the features effectively.') }}</p>
            </div>
    
            <!-- Navigation Tabs -->
            <div class="flex flex-wrap items-center gap-2 mb-8 border-b border-slate-200 pb-4">
                <button @click="activeTab = 'dashboard'" 
                    :class="activeTab === 'dashboard' ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                    {{ __('Floor Plan & Tables') }}
                </button>
                <button @click="activeTab = 'operations'" 
                    :class="activeTab === 'operations' ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ __('Live Operations') }}
                </button>
                <button @click="activeTab = 'waitlist'" 
                    :class="activeTab === 'waitlist' ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ __('Waitlist') }}
                </button>
                <button @click="activeTab = 'admin'" 
                    :class="activeTab === 'admin' ? 'bg-slate-900 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'"
                    class="px-4 py-2.5 rounded-xl text-sm font-bold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    {{ __('Admin & Reports') }}
                </button>
            </div>
    
            <div class="space-y-6">
                <!-- Tab Content: Dashboard / Floor Plan -->
                <div x-show="activeTab === 'dashboard'" x-transition.opacity.duration.300ms class="space-y-4">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl border border-slate-100">
                        <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                            </span>
                            {{ __('Interactive Floor Plan Builder') }}
                        </h2>
                        
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ __('The Dashboard serves as the central hub for managing your restaurant layout. You can completely customize the canvas to match your physical floor plan.') }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h3 class="font-bold text-slate-800 text-sm">{{ __('1. Placing Tables & Sections') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('To add tables or colored sections to your floor plan, you must first create them in the left-hand inventory panel using the "Create New Table" or "Create New Section" buttons. Once created, simply drag and drop them directly onto the grid on the right side.') }}</p>
                            </div>
                            
                            <div class="space-y-3">
                                <h3 class="font-bold text-slate-800 text-sm">{{ __('2. Edit Modes') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('Use the EDIT MODE toggle on the left panel to switch between editing Tables or Sections. You can only drag, resize, or delete items that match your active Edit Mode. The inactive items will become blurred and unclickable.') }}</p>
                            </div>
                            
                            <div class="space-y-3">
                                <h3 class="font-bold text-slate-800 text-sm">{{ __('3. Resizing, Moving, and Bookmarking') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('Once placed on the grid, you can move items by dragging them to a new square. To resize a colored section, hover over its bottom-right corner until you see the circular handle, click, and drag it to expand or shrink the section. You can also bookmark tables as templates so they duplicate when dragged out of the inventory.') }}</p>
                            </div>
                            
                            <div class="space-y-3">
                                <h3 class="font-bold text-slate-800 text-sm">{{ __('4. Canvas Dimensions') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('Need a bigger room? Click the Canvas Dimensions button above the grid to increase the number of columns and rows. You cannot shrink the grid if there are tables or sections currently occupying the outermost edges.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Tab Content: Live Operations -->
                <div x-show="activeTab === 'operations'" x-transition.opacity.duration.300ms class="space-y-4" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl border border-slate-100">
                        <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </span>
                            {{ __('Live Table Operations') }}
                        </h2>
                        
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ __('The system provides real-time state tracking of your physical tables. Tables color-coordinate to indicate their current status.') }}</p>
                        
                        <div class="space-y-6">
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-white border-2 border-slate-300"></div> {{ __('Seating a Customer') }}
                                </h3>
                                <p class="text-slate-600 text-sm mt-2">{{ __('When a customer arrives, click on an Available (white) table on the dashboard grid. A modal will appear allowing you to input the number of guests (Pax). Once confirmed, the table will turn green (Occupied).') }}</p>
                            </div>
                            
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div> {{ __('Modifying Active Sessions') }}
                                </h3>
                                <p class="text-slate-600 text-sm mt-2">{{ __('If an Occupied table gains more guests during their meal, you can click the green table and adjust the total guest count without stopping their timer.') }}</p>
                            </div>
                            
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-rose-500"></div> {{ __('Finishing a Meal') }}
                                </h3>
                                <p class="text-slate-600 text-sm mt-2">{{ __('When the guests have finished their meal and the table is cleared, click on the Occupied (green) table and select checkout. The table will revert to Available, automatically logging the session duration and Pax into the database for your daily reports.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
    
                <!-- Tab Content: Waitlist -->
                <div x-show="activeTab === 'waitlist'" x-transition.opacity.duration.300ms class="space-y-4" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl border border-slate-100">
                        <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            {{ __('Managing the Waitlist') }}
                        </h2>
                        
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ __('The Waitlist page helps you manage customer queues during peak hours. It provides a real-time list of waiting parties, ordered automatically by arrival time.') }}</p>
                        
                        <ul class="space-y-4">
                            <li class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0 mt-0.5">1</div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">{{ __('Adding Customers') }}</h4>
                                    <p class="text-slate-500 text-sm mt-1">{{ __('Use the "Add to Waitlist" button to log a new party. You will need their Name, Phone Number (optional), and the number of Pax.') }}</p>
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0 mt-0.5">2</div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">{{ __('Seating from Waitlist') }}</h4>
                                    <p class="text-slate-500 text-sm mt-1">{{ __('When a table becomes available, find the customer in the waitlist and click "Seat". You will be prompted to select which specific table number they are being seated at. Alternatively, you can seat them directly from the Dashboard modal!') }}</p>
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center font-bold shrink-0 mt-0.5">3</div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm">{{ __('Cancellations') }}</h4>
                                    <p class="text-slate-500 text-sm mt-1">{{ __('If a party leaves before being seated, click "Cancel" to remove them from the active queue. They will still be logged as cancelled in the database for analytics.') }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
    
                <!-- Tab Content: Admin -->
                <div x-show="activeTab === 'admin'" x-transition.opacity.duration.300ms class="space-y-4" style="display: none;">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-soft-xl border border-slate-100">
                        <h2 class="text-xl font-black text-slate-900 mb-4 flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </span>
                            {{ __('Admin & Reporting') }}
                        </h2>
                        
                        <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ __('This section contains powerful analytics and security controls designed specifically for store managers, franchise owners, and super admins.') }}</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <h3 class="font-bold text-slate-800 mb-2">{{ __('Reports & Database') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('The database tracks every single visit, table duration, and waitlist cancellation. You can view high-level metrics via the Overall tab, drill down into Daily/Monthly/Yearly reports, analyze peak traffic times in Customer Habits, or export the raw tables for external review.') }}</p>
                            </div>
                            
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <h3 class="font-bold text-slate-800 mb-2">{{ __('Role & User Permission') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('Super Admins have full access to all features, while normal Admins and Staff adhere to a customizable Default Template. The permissions page allows you to mass-overwrite user access globally, or click into a specific staff member and give them custom exemptions (indicated by an exclamation mark).') }}</p>
                            </div>
                            
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <h3 class="font-bold text-slate-800 mb-2">{{ __('Account Management') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('Super Admins can create new user accounts and assign them to specific Outlets. Once a user logs in, they only interact with the floor plan and data belonging exclusively to their assigned outlet.') }}</p>
                            </div>
                            
                            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                                <h3 class="font-bold text-slate-800 mb-2">{{ __('Language & Profile') }}</h3>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ __('Users can securely update their personal passwords in the Profile tab. They can also seamlessly toggle between English and Indonesian using the language switcher in the top navigation bar at any time.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </main>
    </div>
</x-app-layout>"""

with open(file_path, "w", encoding="utf-8") as f:
    f.write(new_html)

import json
lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
with open(lang_path, "r", encoding="utf-8") as f:
    id_dict = json.load(f)

# Update id.json with all the newly added translations
updates = {
    "The Dashboard serves as the central hub for managing your restaurant layout. You can completely customize the canvas to match your physical floor plan.": "Dasbor (Beranda) berfungsi sebagai pusat utama untuk mengelola tata letak restoran Anda. Anda dapat sepenuhnya menyesuaikan kanvas agar sesuai dengan denah fisik Anda.",
    "1. Placing Tables & Sections": "1. Menempatkan Meja & Area",
    "To add tables or colored sections to your floor plan, you must first create them in the left-hand inventory panel using the \"Create New Table\" or \"Create New Section\" buttons. Once created, simply drag and drop them directly onto the grid on the right side.": "Untuk menambahkan meja atau area berwarna ke denah lantai Anda, Anda harus terlebih dahulu membuatnya di panel inventaris sebelah kiri menggunakan tombol 'Bikin Meja Baru' atau 'Bikin Area Baru'. Setelah dibuat, cukup seret dan lepas langsung ke kotak di sebelah kanan.",
    "3. Resizing, Moving, and Bookmarking": "3. Mengubah Ukuran, Memindahkan, dan Mem-bookmark",
    "Once placed on the grid, you can move items by dragging them to a new square. To resize a colored section, hover over its bottom-right corner until you see the circular handle, click, and drag it to expand or shrink the section. You can also bookmark tables as templates so they duplicate when dragged out of the inventory.": "Setelah ditempatkan di kotak, Anda dapat memindahkan item dengan menyeretnya ke kotak baru. Untuk mengubah ukuran area berwarna, arahkan kursor ke sudut kanan bawahnya, klik, dan seret. Anda juga dapat mem-bookmark meja sebagai templat sehingga meja tersebut digandakan saat diseret keluar dari inventaris.",
    "Live Table Operations": "Operasional Meja Langsung",
    "The system provides real-time state tracking of your physical tables. Tables color-coordinate to indicate their current status.": "Sistem ini menyediakan pelacakan status meja fisik Anda secara real-time. Meja dikoordinasikan berdasarkan warna untuk menunjukkan status mereka saat ini.",
    "Seating a Customer": "Mendudukkan Pelanggan",
    "When a customer arrives, click on an Available (white) table on the dashboard grid. A modal will appear allowing you to input the number of guests (Pax). Once confirmed, the table will turn green (Occupied).": "Saat pelanggan tiba, klik meja Kosong (putih) pada denah. Sebuah jendela akan muncul memungkinkan Anda memasukkan jumlah tamu (Pax). Setelah dikonfirmasi, meja akan berubah menjadi hijau (Terisi).",
    "Modifying Active Sessions": "Mengubah Sesi Aktif",
    "If an Occupied table gains more guests during their meal, you can click the green table and adjust the total guest count without stopping their timer.": "Jika meja yang terisi mendapat tambahan tamu saat mereka makan, Anda dapat mengklik meja hijau tersebut dan menyesuaikan total jumlah tamu tanpa menghentikan timer mereka.",
    "Finishing a Meal": "Menyelesaikan Makan",
    "When the guests have finished their meal and the table is cleared, click on the Occupied (green) table and select checkout. The table will revert to Available, automatically logging the session duration and Pax into the database for your daily reports.": "Saat tamu selesai makan dan meja telah dibersihkan, klik meja Terisi (hijau) dan pilih selesai. Meja akan kembali Kosong, dan sistem otomatis mencatat durasi serta Pax ke dalam basis data untuk laporan harian.",
    "Managing the Waitlist": "Mengelola Daftar Tunggu",
    "The Waitlist page helps you manage customer queues during peak hours. It provides a real-time list of waiting parties, ordered automatically by arrival time.": "Halaman Daftar Tunggu membantu Anda mengelola antrean pelanggan pada jam sibuk. Menyediakan daftar tamu yang menunggu secara real-time, diurutkan otomatis berdasarkan waktu kedatangan.",
    "Adding Customers": "Menambah Pelanggan",
    "Use the \"Add to Waitlist\" button to log a new party. You will need their Name, Phone Number (optional), and the number of Pax.": "Gunakan tombol 'Tambah ke Daftar Tunggu' untuk mencatat rombongan baru. Anda perlu memasukkan Nama, Nomor HP (opsional), dan jumlah Pax.",
    "Seating from Waitlist": "Mendudukkan dari Daftar Tunggu",
    "When a table becomes available, find the customer in the waitlist and click \"Seat\". You will be prompted to select which specific table number they are being seated at. Alternatively, you can seat them directly from the Dashboard modal!": "Ketika ada meja kosong, temukan pelanggan di daftar tunggu dan klik 'Dudukkan'. Anda akan diminta memilih nomor meja spesifik. Alternatifnya, Anda bisa mendudukkan mereka langsung dari modal Beranda!",
    "Cancellations": "Pembatalan",
    "If a party leaves before being seated, click \"Cancel\" to remove them from the active queue. They will still be logged as cancelled in the database for analytics.": "Jika rombongan pergi sebelum duduk, klik 'Batal' untuk menghapus mereka dari antrean aktif. Mereka akan tetap tercatat sebagai dibatalkan di basis data untuk keperluan analitik.",
    "Admin & Reporting": "Admin & Pelaporan",
    "This section contains powerful analytics and security controls designed specifically for store managers, franchise owners, and super admins.": "Bagian ini berisi analitik canggih dan kontrol keamanan yang dirancang khusus untuk manajer toko, pemilik waralaba, dan super admin.",
    "Reports & Database": "Laporan & Basis Data",
    "The database tracks every single visit, table duration, and waitlist cancellation. You can view high-level metrics via the Overall tab, drill down into Daily/Monthly/Yearly reports, analyze peak traffic times in Customer Habits, or export the raw tables for external review.": "Basis data melacak setiap kunjungan, durasi meja, dan pembatalan antrean. Anda dapat melihat metrik tingkat atas melalui tab Keseluruhan, menelusuri laporan Harian/Bulanan/Tahunan, menganalisis waktu sibuk di Kebiasaan Pelanggan, atau mengekspor data untuk tinjauan eksternal.",
    "Role & User Permission": "Peran & Izin Pengguna",
    "Super Admins have full access to all features, while normal Admins and Staff adhere to a customizable Default Template. The permissions page allows you to mass-overwrite user access globally, or click into a specific staff member and give them custom exemptions (indicated by an exclamation mark).": "Super Admin memiliki akses penuh, sedangkan Admin biasa dan Staf mengikuti Templat Default yang dapat disesuaikan. Halaman izin memungkinkan Anda menimpa akses secara massal, atau mengklik staf tertentu untuk memberikan pengecualian khusus (ditandai dengan tanda seru).",
    "Account Management": "Manajemen Akun",
    "Super Admins can create new user accounts and assign them to specific Outlets. Once a user logs in, they only interact with the floor plan and data belonging exclusively to their assigned outlet.": "Super Admin dapat membuat akun pengguna baru dan menetapkannya ke Outlet tertentu. Setelah pengguna masuk, mereka hanya dapat berinteraksi dengan denah dan data yang secara eksklusif milik outlet mereka.",
    "Language & Profile": "Bahasa & Profil",
    "Users can securely update their personal passwords in the Profile tab. They can also seamlessly toggle between English and Indonesian using the language switcher in the top navigation bar at any time.": "Pengguna dapat dengan aman memperbarui kata sandi pribadi mereka di tab Profil. Mereka juga dapat beralih antara bahasa Inggris dan Indonesia menggunakan tombol bahasa di navigasi atas kapan saja."
}

for k, v in updates.items():
    id_dict[k] = v

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)

print("Tutorial rewritten.")
