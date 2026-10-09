file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\dashboard.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

replacements = {
    ">Table Edit<": ">{{ __('Table Edit') }}<",
    ">Section Edit<": ">{{ __('Section Edit') }}<",
    "Unplaced Inventory": "{{ __('Unplaced Inventory') }}",
    "Drag to floor or click card to edit/delete": "{{ __('Drag to floor or click card to edit/delete') }}",
    "Create New Section": "{{ __('Create New Section') }}",
    "Unplaced Sections": "{{ __('Unplaced Sections') }}",
    "No Available Sections": "{{ __('No Available Sections') }}",
    "Table &mdash;": "{{ __('Table &mdash;') }}",
    "Max Capacity:": "{{ __('Max Capacity:') }}",
    "Pax": "{{ __('Pax') }}",
    "Guest Seating": "{{ __('Guest Seating') }}",
    "Waitlist": "{{ __('Waitlist') }}",
    "Edit Specs": "{{ __('Edit Specs') }}",
    "Total\n                                            Guests": "{{ __('Total Guests') }}",
    "{{ __('{{ __('CLEAR') }}') }}": "{{ __('CLEAR') }}",
    ">CLEAR<": ">{{ __('CLEAR') }}<",
    "Seat Guests & Start Session": "{{ __('Seat Guests & Start Session') }}",
    "Waitlist Customers (Pax &le; ": "{{ __('Waitlist Customers (Pax &le; ') }}",
    "No waitlist customers.": "{{ __('No waitlist customers.') }}",
    "Table Number": "{{ __('Table Number') }}",
    "e.g. T-01": "{{ __('e.g. T-01') }}",
    "Capacity (Pax)": "{{ __('Capacity (Pax)') }}",
    "Capacity\n                                    (Pax)": "{{ __('Capacity (Pax)') }}",
    "Capacity\n                                        (Pax)": "{{ __('Capacity (Pax)') }}",
    ">Width:<": ">{{ __('Width:') }}<",
    ">Height:<": ">{{ __('Height:') }}<",
    "Save Table Specifications": "{{ __('Save Table Specifications') }}",
    "Delete Table Permanently": "{{ __('Delete Table Permanently') }}",
    "No Available Tables": "{{ __('No Available Tables') }}"
}

for old_str, new_str in replacements.items():
    content = content.replace(old_str, new_str)

# Special fix for Total Guests line breaking
content = content.replace("Total\n                                            Guests", "{{ __('Total Guests') }}")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Dashboard replaced.")
