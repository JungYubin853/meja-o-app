file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\waitlist.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    ">Waitlist Management<": ">{{ __('Waitlist Management') }}<",
    " Waiting": " {{ __('Waiting') }}",
    "Add New Party to Queue": "{{ __('Add New Party to Queue') }}",
    "Customer Name (e.g. John)": "{{ __('Customer Name (e.g. John)') }}",
    "Phone (Optional)": "{{ __('Phone (Optional)') }}",
    "Guests (Pax)": "{{ __('Guests (Pax)') }}",
    "Add to Queue": "{{ __('Add to Queue') }}",
    "Live Waiting Queue": "{{ __('Live Waiting Queue') }}",
    "Arrived": "{{ __('Arrived') }}",
    "Waiting for": "{{ __('Waiting for') }}",
    "Pax": "{{ __('Pax') }}",
    "Cancel this party from the waiting queue?": "{{ __('Cancel this party from the waiting queue?') }}",
    "No parties currently in the waitlist.": "{{ __('No parties currently in the waitlist.') }}"
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
    "Waiting": "Menunggu",
    "Add New Party to Queue": "Tambah Rombongan Baru ke Antrean",
    "Customer Name (e.g. John)": "Nama Pelanggan (mis. John)",
    "Phone (Optional)": "No. HP (Opsional)",
    "Guests (Pax)": "Tamu (Orang)",
    "Add to Queue": "Tambah ke Antrean",
    "Live Waiting Queue": "Antrean Tunggu Langsung",
    "Arrived": "Tiba",
    "Waiting for": "Menunggu selama",
    "Cancel this party from the waiting queue?": "Batalkan rombongan ini dari antrean?",
    "No parties currently in the waitlist.": "Tidak ada rombongan di daftar tunggu saat ini."
}
for k, v in additions.items():
    id_dict[k] = v

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)

print("Waitlist translated.")
