with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Models\VisitorLog.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("'outlet_id',", "'outlet_id',\n        'table_id',")

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Models\VisitorLog.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Added table_id to fillable")
