file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\permission.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

old_init = """                permissions: {},

                get displayedUsers() {"""

new_init = """                permissions: {},

                init() {
                    const params = new URLSearchParams(window.location.search);
                    const email = params.get('email');
                    if (email) {
                        this.searchEmail = email;
                        this.searchUser();
                    }
                },

                get displayedUsers() {"""

content = content.replace(old_init, new_init)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated permission.blade.php to read email from URL.")
