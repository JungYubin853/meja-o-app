import os
import json

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    "Unplaced\n                                        Inventory": "{{ __('Unplaced Inventory') }}",
    "Edit Inventory Table": "{{ __('Edit Inventory Table') }}",
    "Edit table specs or delete directly from\n                                inventory.": "{{ __('Edit table specs or delete directly from inventory.') }}",
    "Table Name / No.": "{{ __('Table Name / No.') }}",
    "Confirm & Add to Inventory": "{{ __('Confirm & Add to Inventory') }}",
    "Drag Me": "{{ __('Drag me') }}"
}
for k, v in replacements.items():
    content = content.replace(k, v)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
with open(lang_path, "r", encoding="utf-8") as f:
    id_dict = json.load(f)

id_dict.update({
    "Unplaced Inventory": "Area Belum Ditempatkan",  # the user specifically asked for this
    "Edit Inventory Table": "Edit Meja Inventaris",
    "Edit table specs or delete directly from inventory.": "Edit spesifikasi meja atau hapus langsung dari inventaris.",
    "Table Name / No.": "Nomer Meja (Opt)",
    "Confirm & Add to Inventory": "Simpan Perubahan",
    "Drag me": "Seret"
})

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)
print("Dashboard fixed.")
