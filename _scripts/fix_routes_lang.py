file_path = r"C:\Users\LEGION\Herd\meja-o-app\routes\web.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

new_route = """
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
    }
    return back();
});

Route::middleware(['auth'])->group(function () {"""

content = content.replace("Route::middleware(['auth'])->group(function () {", new_route)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Added language switch route.")
