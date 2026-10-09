file_path = r"C:\Users\LEGION\Herd\meja-o-app\resources\views\components\app-layout.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Add permission checks for tutorial and calendar in mobile menu
old_mobile_tutorial = """                            <!-- Tutorial Link -->
                            <a href="/tutorial\""""
new_mobile_tutorial = """                            <!-- Tutorial Link -->
                            @if(Auth::user()->hasPermission('nav_tutorial'))
                            <a href="/tutorial\""""
content = content.replace(old_mobile_tutorial, new_mobile_tutorial)

old_mobile_tutorial_end = """                                <span>Tutorial & Guidelines</span>
                            </a>
                            <!-- Calendar Link -->"""
new_mobile_tutorial_end = """                                <span>Tutorial & Guidelines</span>
                            </a>
                            @endif
                            <!-- Calendar Link -->"""
content = content.replace(old_mobile_tutorial_end, new_mobile_tutorial_end)

old_mobile_calendar = """                            <!-- Calendar Link -->
                            <a href="/calendar\""""
new_mobile_calendar = """                            <!-- Calendar Link -->
                            @if(Auth::user()->hasPermission('nav_calendar'))
                            <a href="/calendar\""""
content = content.replace(old_mobile_calendar, new_mobile_calendar)

old_mobile_calendar_end = """                                <span>Calendar</span>
                            </a>
                        </nav>"""
new_mobile_calendar_end = """                                <span>Calendar</span>
                            </a>
                            @endif
                        </nav>"""
content = content.replace(old_mobile_calendar_end, new_mobile_calendar_end)

# Add permission checks for tutorial and calendar in desktop menu
old_desktop_tutorial = """                            <!-- Tutorial Link -->
                            <a href="/tutorial\""""
new_desktop_tutorial = """                            <!-- Tutorial Link -->
                            @if(Auth::user()->hasPermission('nav_tutorial'))
                            <a href="/tutorial\""""
content = content.replace(old_desktop_tutorial, new_desktop_tutorial)

old_desktop_tutorial_end = """                                  <span>Tutorial & Guidelines</span>
                              </a>
                              <!-- Calendar Link -->"""
new_desktop_tutorial_end = """                                  <span>Tutorial & Guidelines</span>
                              </a>
                              @endif
                              <!-- Calendar Link -->"""
content = content.replace(old_desktop_tutorial_end, new_desktop_tutorial_end)

old_desktop_calendar = """                              <!-- Calendar Link -->
                              <a href="/calendar\""""
new_desktop_calendar = """                              <!-- Calendar Link -->
                              @if(Auth::user()->hasPermission('nav_calendar'))
                              <a href="/calendar\""""
content = content.replace(old_desktop_calendar, new_desktop_calendar)

old_desktop_calendar_end = """                                  <span>Calendar</span>
                              </a>
                          </nav>"""
new_desktop_calendar_end = """                                  <span>Calendar</span>
                              </a>
                              @endif
                          </nav>"""
content = content.replace(old_desktop_calendar_end, new_desktop_calendar_end)


with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated app-layout.blade.php with permissions")
