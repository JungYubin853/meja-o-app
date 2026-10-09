file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\account-management.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Group the filters and pagination into one container so they align properly
old_structure = """                        <!-- Filters -->
                        <form method="GET" action="/account-management" class="flex items-center gap-2">"""

new_structure = """                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <!-- Filters -->
                            <form method="GET" action="/account-management" class="flex items-center gap-2">"""

content = content.replace(old_structure, new_structure)

old_pagination_start = """                        <!-- Pagination Controls -->
                        <div class="flex items-center gap-4 ml-auto">"""
new_pagination_start = """                            <!-- Pagination Controls -->
                            <div class="flex items-center gap-4">"""
content = content.replace(old_pagination_start, new_pagination_start)

# Add closing div for the new wrapper before the table
old_table_start = """                    </div>

                <div class="overflow-x-auto touch-scroll">"""
new_table_start = """                        </div>
                    </div>

                <div class="overflow-x-auto touch-scroll">"""
content = content.replace(old_table_start, new_table_start)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Grouped header controls.")
