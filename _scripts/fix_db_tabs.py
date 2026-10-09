import os
import re
import json

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace the tabs safely
content = re.sub(r'>\s*Overall\s*</a>', r'>{{ __(\'Overall\') }}</a>', content)
content = re.sub(r'>\s*Daily Report\s*</a>', r'>{{ __(\'Daily Report\') }}</a>', content)
content = re.sub(r'>\s*Monthly Report\s*</a>', r'>{{ __(\'Monthly Report\') }}</a>', content)
content = re.sub(r'>\s*Yearly Report\s*</a>', r'>{{ __(\'Yearly Report\') }}</a>', content)
content = re.sub(r'>\s*Waitlist\s*</a>', r'>{{ __(\'Waitlist\') }}</a>', content)
content = re.sub(r'>\s*Customer Habits\s*</a>', r'>{{ __(\'Customer Habits\') }}</a>', content)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

# Update lang/id.json just in case
lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
with open(lang_path, "r", encoding="utf-8") as f:
    id_dict = json.load(f)

id_dict.update({
    "Overall": "Keseluruhan",
    "Daily Report": "Laporan Harian",
    "Monthly Report": "Laporan Bulanan",
    "Yearly Report": "Laporan Tahunan",
    "Waitlist": "Daftar Tunggu",
    "Customer Habits": "Kebiasaan Pelanggan",
    "Tutorial & Guidelines": "Tutorial & Panduan",
    "Welcome to the My Kopi-O Table Management System. Below is a comprehensive guide to help Staff and Admins navigate and utilize all the features effectively.": "Selamat datang di Sistem Manajemen Meja My Kopi-O. Berikut adalah panduan lengkap untuk membantu Staf dan Admin dalam mengoperasikan serta memanfaatkan seluruh fitur secara efektif.",
    "Floor Plan & Tables": "Denah & Meja",
    "Live Operations": "Operasional Langsung",
    "Admin & Reports": "Admin & Laporan",
    "Interactive Floor Plan Builder": "Pembuat Denah Interaktif",
    "2. Edit Modes": "2. Mode Edit",
    "Use the EDIT MODE toggle on the left panel to switch between editing Tables or Sections. You can only drag, resize, or delete items that match your active Edit Mode. The inactive items will become blurred and unclickable.": "Gunakan tombol MODE EDIT di panel kiri untuk beralih antara mengedit Meja atau Area. Anda hanya dapat menyeret, mengubah ukuran, atau menghapus item yang sesuai dengan Mode Edit aktif Anda. Item yang tidak aktif akan menjadi buram dan tidak dapat diklik.",
    "3. Resizing & Moving": "3. Mengubah Ukuran & Memindahkan",
    "Once placed on the grid, you can move items by dragging them to a new square. To resize a colored section, hover over its bottom-right corner until you see the circular handle, click, and drag it to expand or shrink the section.": "Setelah ditempatkan di kotak, Anda dapat memindahkan item dengan menyeretnya ke kotak baru. Untuk mengubah ukuran area berwarna, arahkan kursor ke sudut kanan bawahnya hingga Anda melihat pegangan bundar, klik, dan seret untuk memperluas atau memperkecil area tersebut.",
    "4. Canvas Dimensions": "4. Ukuran Kanvas",
    "Need a bigger room? Click the Canvas Dimensions button above the grid to increase the number of columns and rows. You cannot shrink the grid if there are tables or sections currently occupying the outermost edges.": "Butuh ruangan lebih besar? Klik tombol Ukuran Kanvas di atas kotak untuk menambah jumlah kolom dan baris. Anda tidak dapat memperkecil kotak jika ada meja atau area yang saat ini menempati tepi terluar."
})
with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)

print("Database fixed.")
