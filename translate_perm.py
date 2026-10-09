file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    "Select a user from the left to edit their specific permissions.": "{{ __('Select a user from the left to edit their specific permissions.') }}",
    "Current User": "{{ __('Current User') }}",
    "Editing permissions for:": "{{ __('Editing permissions for:') }}",
    "This user has custom settings.": "{{ __('This user has custom settings.') }}",
    "This user strictly follows the default template.": "{{ __('This user strictly follows the default template.') }}",
    "MAIN NAVIGATION": "{{ __('MAIN NAVIGATION') }}",
    "Dashboard - 2D Floor Canvas": "{{ __('Dashboard - 2D Floor Canvas') }}",
    "Waitlist": "{{ __('Waitlist') }}",
    "Reports & Database": "{{ __('Reports & Database') }}",
    "Tutorial & Guidelines": "{{ __('Tutorial & Guidelines') }}",
    "Calendar": "{{ __('Calendar') }}",
    "Profile": "{{ __('Profile') }}",
    "Account Management": "{{ __('Account Management') }}",
    "Role & User Permission": "{{ __('Role & User Permission') }}",
    "DASHBOARD CONTROLS": "{{ __('DASHBOARD CONTROLS') }}",
    "Guest Seating and Waitlist": "{{ __('Guest Seating and Waitlist') }}",
    "Edit Existing Tables": "{{ __('Edit Existing Tables') }}",
    "Delete Tables": "{{ __('Delete Tables') }}",
    "Create New Table Container": "{{ __('Create New Table Container') }}",
    "REPORTS & DATABASE": "{{ __('REPORTS & DATABASE') }}",
    "Daily Report": "{{ __('Daily Report') }}",
    "Monthly Report": "{{ __('Monthly Report') }}",
    "Waitlist Report": "{{ __('Waitlist Report') }}"
}
for k, v in replacements.items():
    content = content.replace(k, v)
with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

import json
lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
with open(lang_path, "r", encoding="utf-8") as f:
    id_dict = json.load(f)

id_dict.update({
    "Select a user from the left to edit their specific permissions.": "Pilih pengguna dari kiri untuk mengedit izin spesifik mereka.",
    "Current User": "Pengguna Saat Ini",
    "Editing permissions for:": "Mengedit izin untuk:",
    "This user has custom settings.": "Pengguna ini memiliki pengaturan kustom.",
    "This user strictly follows the default template.": "Pengguna ini secara ketat mengikuti template default.",
    "MAIN NAVIGATION": "NAVIGASI UTAMA",
    "Dashboard - 2D Floor Canvas": "Beranda - Kanvas 2D",
    "Guest Seating and Waitlist": "Tempat Duduk Tamu dan Daftar Tunggu",
    "Edit Existing Tables": "Edit Meja yang Ada",
    "Delete Tables": "Hapus Meja",
    "Create New Table Container": "Bikin Tempat Meja Baru",
    "DASHBOARD CONTROLS": "KONTROL BERANDA",
    "REPORTS & DATABASE": "LAPORAN & BASIS DATA",
    "Daily Report": "Laporan Harian",
    "Monthly Report": "Laporan Bulanan",
    "Waitlist Report": "Laporan Daftar Tunggu"
})
with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)
