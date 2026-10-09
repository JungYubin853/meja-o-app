# -*- coding: utf-8 -*-
import os
import json
import re

views_dir = r"C:\Users\LEGION\Herd\meja-o-app\resources\views"

translations = {
    "Table Edit": "Edit Meja",
    "Section Edit": "Edit Area",
    "Create New Section": "Bikin Area Baru",
    "Unplaced Inventory": "Inventaris Belum Ditempatkan",
    "Unplaced Sections": "Area Belum Ditempatkan",
    "Drag to floor or click card to edit/delete": "Seret ke lantai atau klik kartu untuk edit/hapus",
    "No Available Sections": "Tidak Ada Area Tersedia",
    "Table \u2014": "Meja \u2014",
    "Max Capacity:": "Kapasitas Maks:",
    "Pax": "Orang",
    "Guest Seating": "Tempat Duduk Tamu",
    "Edit Specs": "Edit Spesifikasi",
    "Total Guests": "Total Tamu",
    "CLEAR": "HAPUS",
    "Seat Guests & Start Session": "Dudukkan Tamu & Mulai Sesi",
    "Waitlist Customers (Pax \u2264 ": "Pelanggan Daftar Tunggu (Orang \u2264 ",
    "Waitlist Customers": "Pelanggan Daftar Tunggu",
    "No waitlist customers.": "Tidak ada pelanggan di daftar tunggu.",
    "Table Number": "Nomor Meja",
    "e.g. T-01": "misal T-01",
    "Capacity (Pax)": "Kapasitas (Orang)",
    "Width:": "Lebar:",
    "Height:": "Tinggi:",
    "Save Table Specifications": "Simpan Spesifikasi Meja",
    "Delete Table Permanently": "Hapus Meja Permanen",
    "Canvas Size": "Ukuran Kanvas",
    "No Available Tables": "Tidak Ada Meja Tersedia",
    "Available": "Tersedia",
    "Occupied": "Terisi",
    
    # Waitlist page
    "Waitlist Management": "Manajemen Daftar Tunggu",
    "Customer Name": "Nama Pelanggan",
    "Number of Guests": "Jumlah Tamu",
    "Add to Waitlist": "Tambah ke Daftar Tunggu",
    "Current Waitlist": "Daftar Tunggu Saat Ini",
    "Search customer...": "Cari pelanggan...",
    "No customers currently on the waitlist.": "Tidak ada pelanggan di daftar tunggu saat ini.",
    "Waiting Time": "Waktu Tunggu",
    "Seat": "Dudukkan",
    "Cancel": "Batal",
    
    # Profile
    "Profile Information": "Informasi Profil",
    "Update your account's profile information and email address.": "Perbarui informasi profil dan alamat email akun Anda.",
    "Full Name": "Nama Lengkap",
    "Email Address": "Alamat Email",
    "Role": "Peran",
    "Assigned Outlet": "Outlet Ditugaskan",
    "Save Changes": "Simpan Perubahan",
    "Change Password": "Ubah Kata Sandi",
    "Ensure your account is using a long, random password to stay secure.": "Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.",
    "Current Password": "Kata Sandi Saat Ini",
    "New Password": "Kata Sandi Baru",
    "Confirm Password": "Konfirmasi Kata Sandi",
    
    # Account Management
    "User List": "Daftar Pengguna",
    "Create New Account": "Bikin Akun Baru",
    "Users": "Pengguna",
    "Search name or email...": "Cari nama atau email...",
    "All Roles": "Semua Peran",
    "All Outlets": "Semua Outlet",
    "Delete User": "Hapus Pengguna",
    "Are you sure you want to delete this user?": "Apakah Anda yakin ingin menghapus pengguna ini?",
    "Password": "Kata Sandi",
    
    # Permissions
    "Search User": "Cari Pengguna",
    "Bulk Overwrite Users": "Timpa Massal Pengguna",
    "All Staff": "Semua Staf",
    "All Admins": "Semua Admin",
    "Overwrite existing staff": "Timpa staf yang ada",
    "Overwrite existing admins": "Timpa admin yang ada",
    "Base Default Templates": "Template Default Dasar",
    "Staff Defaults": "Default Staf",
    "Admin Defaults": "Default Admin",
    "Set baseline for newly created staff": "Atur dasar untuk staf yang baru dibuat",
    "Set baseline for newly created admins": "Atur dasar untuk admin yang baru dibuat",
    "Custom Permissions Detected": "Izin Kustom Terdeteksi",
    "The following users have custom access settings that differ from the default template:": "Pengguna berikut memiliki pengaturan akses kustom yang berbeda dari template default:",
    "Do you want to overwrite everyone to make them exactly the same, or keep these custom users as they are?": "Apakah Anda ingin menimpa semua orang agar sama persis, atau biarkan pengguna kustom ini apa adanya?",
    "Overwrite Everyone": "Timpa Semua Orang",
    "Keep Custom Users": "Biarkan Pengguna Kustom",
    "Access Control Settings": "Pengaturan Kontrol Akses",
    "Changes will apply to newly created accounts.": "Perubahan akan diterapkan pada akun yang baru dibuat.",
    "Changes will overwrite all existing accounts.": "Perubahan akan menimpa semua akun yang ada.",
    "Save Permissions": "Simpan Izin",
    
    # Database
    "Visitor Analytics & Database": "Analitik Pengunjung & Basis Data",
    "Total Visitors": "Total Pengunjung",
    "Avg. Party Size": "Rata-rata Ukuran Rombongan",
    "Total Sessions": "Total Sesi",
    "Avg. Wait Time": "Rata-rata Waktu Tunggu",
    "Hourly Traffic": "Lalu Lintas Per Jam",
    "Daily Traffic": "Lalu Lintas Harian",
    "Monthly Traffic": "Lalu Lintas Bulanan",
    "Top Tables": "Meja Terpopuler",
    "Recent Sessions": "Sesi Terbaru",
    "Export Excel (CSV)": "Ekspor Excel (CSV)",
    "Table": "Meja",
    "Date & Time": "Tanggal & Waktu",
    "Duration": "Durasi",
    "Status": "Status",
    "Completed": "Selesai",
    "Cancelled": "Dibatalkan",
    "Rows per page:": "Baris per halaman:",
    "Previous Page": "Halaman Sebelumnya",
    "Next Page": "Halaman Berikutnya",
    
    # Calendar
    "Shift Scheduling & Events": "Penjadwalan Shift & Acara",
    
    # Tutorial
    "How to use Meja-O": "Cara menggunakan Meja-O",
    "Floor Canvas Setup": "Pengaturan Kanvas Lantai",
    "Adding Tables": "Menambahkan Meja",
    "Resizing & Moving": "Mengubah Ukuran & Memindahkan"
}

lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
id_dict = {}
if os.path.exists(lang_path):
    with open(lang_path, "r", encoding="utf-8") as f:
        id_dict = json.load(f)

for eng, indo in translations.items():
    id_dict[eng] = indo

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)

files = [
    "dashboard.blade.php",
    "waitlist.blade.php",
    "database.blade.php",
    "tutorial.blade.php",
    "calendar.blade.php",
    "profile.blade.php",
    "account-management.blade.php",
    "permission.blade.php",
    r"components\app-layout.blade.php"
]

def wrap_translation(match):
    prefix = match.group(1)
    text = match.group(2)
    suffix = match.group(3)
    if "{{ __('" in text or "{!! __('" in text:
        return match.group(0)
    return f"{prefix}{{{{ __('{text}') }}}}{suffix}"

for file in files:
    path = os.path.join(views_dir, file)
    if not os.path.exists(path):
        continue
        
    with open(path, "r", encoding="utf-8") as f:
        content = f.read()
    
    for eng in translations.keys():
        escaped_eng = re.escape(eng)
        
        pattern_tag = r'(>[\s]*)(' + escaped_eng + r')([\s]*<)'
        content = re.sub(pattern_tag, wrap_translation, content)
        
        pattern_ph = r'(placeholder="[\s]*)(' + escaped_eng + r')([\s]*")'
        content = re.sub(pattern_ph, wrap_translation, content)
        
        pattern_title = r'(title="[\s]*)(' + escaped_eng + r')([\s]*")'
        content = re.sub(pattern_title, wrap_translation, content)
        
        pattern_alpine = r"(')(" + escaped_eng + r")(')"
        content = re.sub(pattern_alpine, wrap_translation, content)
        
    with open(path, "w", encoding="utf-8") as f:
        f.write(content)

print("Batch translation complete!")