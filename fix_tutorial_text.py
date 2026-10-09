file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\tutorial.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    "1. Placing Tables & Sections": "{{ __('1. Placing Tables & Sections') }}",
    "To add tables or colored sections to your floor plan, you must first create them in the left-hand inventory panel using the": "{{ __('To add tables or colored sections to your floor plan, you must first create them in the left-hand inventory panel using the') }}",
    "or": "{{ __('or') }}",
    "buttons. Once created, simply": "{{ __('buttons. Once created, simply') }}",
    "Drag and Drop": "{{ __('Drag and Drop') }}",
    "them directly onto the grid on the right side.": "{{ __('them directly onto the grid on the right side.') }}",
    "2. Edit Specs & Assign Guests": "{{ __('2. Edit Specs & Assign Guests') }}",
    "Click on any placed table on the floor plan to open the": "{{ __('Click on any placed table on the floor plan to open the') }}",
    "Table Action Modal": "{{ __('Table Action Modal') }}",
    ". From here you can update the table's label (e.g., T-01), set max seating capacity, or assign incoming guests to start a dining session.": "{{ __('. From here you can update the table\\'s label (e.g., T-01), set max seating capacity, or assign incoming guests to start a dining session.') }}",
    "3. Waitlist & Analytics": "{{ __('3. Waitlist & Analytics') }}",
    "If the restaurant is full, use the": "{{ __('If the restaurant is full, use the') }}",
    "tab to manage the queue. Once a table frees up, you can seat customers directly from the waitlist. Every seated session is automatically recorded into the": "{{ __('tab to manage the queue. Once a table frees up, you can seat customers directly from the waitlist. Every seated session is automatically recorded into the') }}",
    "Reports & Database": "{{ __('Reports & Database') }}",
    "for performance tracking.": "{{ __('for performance tracking.') }}"
}

for k, v in replacements.items():
    content = content.replace(k, v)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

import json
lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
with open(lang_path, "r", encoding="utf-8") as f:
    id_dict = json.load(f)

additions = {
    "1. Placing Tables & Sections": "1. Menempatkan Meja & Area",
    "To add tables or colored sections to your floor plan, you must first create them in the left-hand inventory panel using the": "Untuk menambahkan meja atau area berwarna ke denah lantai Anda, Anda harus terlebih dahulu membuatnya di panel inventaris sebelah kiri menggunakan tombol",
    "or": "atau",
    "buttons. Once created, simply": "Setelah dibuat, cukup",
    "Drag and Drop": "Seret dan Lepas",
    "them directly onto the grid on the right side.": "langsung ke kotak di sebelah kanan.",
    "2. Edit Specs & Assign Guests": "2. Edit Spesifikasi & Tetapkan Tamu",
    "Click on any placed table on the floor plan to open the": "Klik pada meja mana pun yang ditempatkan di denah lantai untuk membuka",
    "Table Action Modal": "Modal Aksi Meja",
    ". From here you can update the table's label (e.g., T-01), set max seating capacity, or assign incoming guests to start a dining session.": ". Dari sini Anda dapat memperbarui label meja (mis. T-01), mengatur kapasitas maksimum tempat duduk, atau menetapkan tamu yang datang untuk memulai sesi bersantap.",
    "3. Waitlist & Analytics": "3. Daftar Tunggu & Analitik",
    "If the restaurant is full, use the": "Jika restoran penuh, gunakan tab",
    "tab to manage the queue. Once a table frees up, you can seat customers directly from the waitlist. Every seated session is automatically recorded into the": "untuk mengelola antrean. Setelah meja kosong, Anda dapat mendudukkan pelanggan langsung dari daftar tunggu. Setiap sesi yang duduk secara otomatis dicatat ke dalam",
    "Reports & Database": "Laporan & Basis Data",
    "for performance tracking.": "untuk pelacakan performa."
}

for k, v in additions.items():
    id_dict[k] = v

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)

print("Tutorial translated.")
