file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix unclosed @if for Calendar link
old_str = """                                <span>Calendar</span>
                            </a>

                            <!-- 4. Profile Link -->"""
new_str = """                                <span>Calendar</span>
                            </a>
                            @endif

                            <!-- 4. Profile Link -->"""
content = content.replace(old_str, new_str)

old_str_desktop = """                                  <span>Calendar</span>
                              </a>

                              <!-- 4. Profile Link -->"""
new_str_desktop = """                                  <span>Calendar</span>
                              </a>
                              @endif

                              <!-- 4. Profile Link -->"""
content = content.replace(old_str_desktop, new_str_desktop)


with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Fixed unclosed @if tags.")
