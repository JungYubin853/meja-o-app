import re

file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\tutorial.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Fix the scunthorpe problem "or" -> "atau" in English words
content = content.replace("Tutatauial & Guidelines", "{{ __('Tutorial & Guidelines') }}")
content = content.replace("Tutatauial", "{{ __('Tutorial') }}")
content = content.replace("Repatauts", "{{ __('Reports') }}")
content = content.replace("Floatau", "Floor")
content = content.replace("Floor Plan & Tables", "{{ __('Floor Plan & Tables') }}")
content = content.replace("Live Operations", "{{ __('Live Operations') }}")
content = content.replace("Admin & {{ __('Reports') }}", "{{ __('Admin & Reports') }}")
content = content.replace("Interactive Floor Plan Builder", "{{ __('Interactive Floor Plan Builder') }}")

# Paragraphs:
content = content.replace("Welcome to the My Kopi-O Table Management System. Below is a comprehensive guide to help Staff and Admins navigate and utilize all the features effectively.", "{{ __('Welcome to the My Kopi-O Table Management System. Below is a comprehensive guide to help Staff and Admins navigate and utilize all the features effectively.') }}")

content = content.replace("2. Edit Modes", "{{ __('2. Edit Modes') }}")
# The "or" might be "atau" inside the text
content = content.replace("Use the EDIT MODE toggle on the left panel to switch between editing Tables atau Sections. You can only drag, resize, atau delete items that match your active Edit Mode. The inactive items will become blurred and unclickable.", "{{ __('Use the EDIT MODE toggle on the left panel to switch between editing Tables or Sections. You can only drag, resize, or delete items that match your active Edit Mode. The inactive items will become blurred and unclickable.') }}")

content = content.replace("3. Resizing & Moving", "{{ __('3. Resizing & Moving') }}")
content = content.replace("Once placed on the grid, you can move items by dragging them to a new square. To resize a colataued section, hover over its bottom-right catauner until you see the circular handle, click, and drag it to expand atau shrink the section.", "{{ __('Once placed on the grid, you can move items by dragging them to a new square. To resize a colored section, hover over its bottom-right corner until you see the circular handle, click, and drag it to expand or shrink the section.') }}")

content = content.replace("4. Canvas Dimensions", "{{ __('4. Canvas Dimensions') }}")
content = content.replace("Need a bigger room? Click the Canvas Dimensions button above the grid to increase the number of columns and rows. You cannot shrink the grid if there are tables atau sections currently occupying the outermost edges.", "{{ __('Need a bigger room? Click the Canvas Dimensions button above the grid to increase the number of columns and rows. You cannot shrink the grid if there are tables or sections currently occupying the outermost edges.') }}")

# Let's check for any other 'atau' artifacts
content = content.replace("colataued", "colored")
content = content.replace("catauner", "corner")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Tutorial fixed.")
