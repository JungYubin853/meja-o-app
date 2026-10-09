file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# I will extract the exact text by slicing
start_php = content.find("@php\n                        $dateObj = \\Carbon\\Carbon::parse($dateFilter);")
end_badge = content.find("</div>\n                </div>\n\n                <div class=\"flex flex-col md:flex-row gap-4\">")

if start_php != -1 and end_badge != -1:
    # We want to grab everything from start_php to the end of the badge (which is right before `</div>\n                </div>`)
    # Wait, the structure is:
    # </div> (closes badge)
    # </div> (closes top bar)
    
    # Let's find exactly the badge string.
    badge_start = content.find("<div class=\"{{ $bgClass }} rounded-xl border border-{{ $classColor }}-200 shadow-soft-xs px-3 py-1.5 flex items-center gap-3 shrink-0\">")
    # we know the badge has a specific structure:
    # <div class="{{ $bgClass }} ...">
    #     <div ...>
    #         <div ...></div>
    #         <div ...></div>
    #     </div>
    #     <div ...>
    #         @if ... @elseif ... @endif
    #     </div>
    # </div>
    
    # We can use regex to safely capture the badge HTML without capturing the outer Top Bar </div>
    import re
    badge_match = re.search(r"(<div class=\"\{\{ \$bgClass \}\}.*?</div>\n                    </div>)", content, re.DOTALL)
    if badge_match:
        badge_html = badge_match.group(1)
        php_html = content[start_php:badge_start]
        
        # Remove them from their current location
        content = content.replace(php_html, "")
        content = content.replace(badge_html, "")
        
        # Now we insert them into the Main Content Box
        main_box = "<!-- 2. Main Content Box (Tabbed) -->\n                <div class=\"flex-1 bg-white rounded-2xl border border-slate-200/80 shadow-soft-xs overflow-hidden flex flex-col\">"
        
        new_row = f"""
                    <!-- Classification Banner -->
                    {php_html}
                    <div class="px-4 py-3 sm:px-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-widest">Date Classification</div>
                        {badge_html}
                    </div>"""
                    
        content = content.replace(main_box, main_box + new_row)
        
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(content)
        print("Moved badge successfully!")
    else:
        print("Could not match badge HTML")
else:
    print("Could not find start or end")
