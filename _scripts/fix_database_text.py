file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    "Log ID": "{{ __('Log ID') }}",
    "Customer Name": "{{ __('Customer Name') }}",
    "Phone": "{{ __('Phone') }}",
    "Guests": "{{ __('Guests') }}",
    "Started": "{{ __('Started') }}",
    "Ended": "{{ __('Ended') }}",
    "Elapsed": "{{ __('Elapsed') }}",
    "Staff": "{{ __('Staff') }}",
    "Walk-in Guest": "{{ __('Walk-in Guest') }}",
    "In Progress": "{{ __('In Progress') }}",
    "No visitor log records found for this filter criteria.": "{{ __('No visitor log records found for this filter criteria.') }}",
    "Showing ": "{{ __('Showing') }} ",
    " to ": " {{ __('to') }} ",
    " of ": " {{ __('of') }} ",
    " records": " {{ __('records') }}",
    "Previous": "{{ __('Previous') }}",
    "Next": "{{ __('Next') }}",
    "Show:": "{{ __('Show:') }}",
    "{{ __('{{ __('Customer Name') }}') }}": "{{ __('Customer Name') }}"
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
    "Log ID": "ID Log",
    "Phone": "No. HP",
    "Guests": "Tamu",
    "Started": "Mulai",
    "Ended": "Selesai",
    "Elapsed": "Durasi",
    "Staff": "Staf",
    "Walk-in Guest": "Tamu Langsung",
    "In Progress": "Sedang Berlangsung",
    "No visitor log records found for this filter criteria.": "Tidak ada catatan log pengunjung untuk kriteria filter ini.",
    "Showing": "Menampilkan",
    "to": "sampai",
    "of": "dari",
    "records": "catatan",
    "Previous": "Sebelumnya",
    "Next": "Berikutnya",
    "Show:": "Tampilkan:"
}

for k, v in additions.items():
    id_dict[k] = v

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)

print("Database translated.")
