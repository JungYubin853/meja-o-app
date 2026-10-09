import json
import os

lang_path = r"C:\Users\LEGION\Herd\meja-o-app\lang\id.json"
id_dict = {}
if os.path.exists(lang_path):
    with open(lang_path, "r", encoding="utf-8") as f:
        id_dict = json.load(f)

additions = {
    "Table &mdash;": "Meja &mdash;",
    "Waitlist Customers (Pax &le; ": "Pelanggan Daftar Tunggu (Orang &le; "
}

for k, v in additions.items():
    id_dict[k] = v

with open(lang_path, "w", encoding="utf-8") as f:
    json.dump(id_dict, f, indent=4, ensure_ascii=False)
