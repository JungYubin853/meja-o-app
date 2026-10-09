with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Models\VisitorLog.php", "r", encoding="utf-8") as f:
    content = f.read()

replacement = """    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }"""
    
content = content.replace("    public function outlet()\n    {\n        return $this->belongsTo(Outlet::class);\n    }", replacement)

with open(r"C:\Users\LEGION\Herd\meja-o-app\app\Models\VisitorLog.php", "w", encoding="utf-8") as f:
    f.write(content)
print("Added table relation")
