import os
import json

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    ">Overall<": ">{{ __('Overall') }}<",
    ">Daily Report<": ">{{ __('Daily Report') }}<",
    ">Monthly Report<": ">{{ __('Monthly Report') }}<",
    ">Yearly Report<": ">{{ __('Yearly Report') }}<",
    ">Waitlist<": ">{{ __('Waitlist') }}<",
    ">Customer Habits<": ">{{ __('Customer Habits') }}<",
    "Visit Logs": "{{ __('Visit Logs') }}",
    "Gantt Graph": "{{ __('Gantt Graph') }}",
    ">Start Session<": ">{{ __('Start Session') }}<",
    "Time {{ __('Elapsed') }}": "{{ __('Time Elapsed') }}",
    "Waitlist Records Database": "{{ __('Waitlist Records Database') }}",
    " Records": " {{ __('Records') }}"
}
for k, v in replacements.items():
    content = content.replace(k, v)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
with open(lang_path, "r", encoding="utf-8") as f:
    id_dict = json.load(f)

id_dict.update({
    "Overall": "Keseluruhan",
    "Daily Report": "Laporan Harian",
    "Monthly Report": "Laporan Bulanan",
    "Yearly Report": "Laporan Tahunan",
    "Customer Habits": "Kebiasaan Pelanggan",
    "Visit Logs": "Log Kunjungan",
    "Gantt Graph": "Grafik Gantt",
    "Start Session": "Mulai Sesi",
    "Time Elapsed": "Durasi Waktu",
    "Waitlist Records Database": "Basis Data Catatan Daftar Tunggu",
    "Records": "Catatan"
})

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)
print("Database fixed.")
