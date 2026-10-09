file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    "Assign Outlet": "{{ __('Assign Outlet') }}",
    "None (For Super Admin)": "{{ __('None (For Super Admin)') }}",
    "Temporary Password": "{{ __('Temporary Password') }}",
    ">Name<": ">{{ __('Name') }}<",
    ">Email<": ">{{ __('Email') }}<",
    ">Outlet / Brand<": ">{{ __('Outlet / Brand') }}<",
    "Action": "{{ __('Action') }}",
    ">Super Admin<": ">{{ __('Super Admin') }}<",
    ">Admin<": ">{{ __('Admin') }}<",
    ">Staff<": ">{{ __('Staff') }}<"
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
    "Assign Outlet": "Tetapkan Outlet",
    "None (For Super Admin)": "Tidak Ada (Untuk Super Admin)",
    "Temporary Password": "Kata Sandi Sementara",
    "Name": "Nama",
    "Email": "Email",
    "Outlet / Brand": "Outlet / Merek",
    "Action": "Aksi",
    "Super Admin": "Super Admin",
    "Admin": "Admin",
    "Staff": "Staf"
})

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)
