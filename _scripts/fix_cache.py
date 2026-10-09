import re

with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

old_php = """                                // Fetch Holidays for Calendar coloring
                                $allHolidays = \\Illuminate\\Support\\Facades\\Cache::get('public_holidays', []);
                                $holidayMap = [];
                                foreach($allHolidays as $h) {
                                    $hDt = $h['holiday_date'] ?? null;
                                    if($hDt) {
                                        $isNat = $h['is_national_holiday'] ?? true;
                                        $holidayMap[$hDt] = $isNat ? 'holiday' : 'collective';
                                    }
                                }"""

new_php = """                                // Fetch Holidays for Calendar coloring
                                $year = $cDate->year;
                                $holidaysData = \\Illuminate\\Support\\Facades\\Cache::get("holidays_{$year}");
                                $holidayMap = [];
                                if ($holidaysData && isset($holidaysData['data']) && is_array($holidaysData['data'])) {
                                    foreach($holidaysData['data'] as $h) {
                                        $hDt = $h['date'] ?? null;
                                        if($hDt) {
                                            $isCollective = (stripos($h['name'], 'Cuti Bersama') !== false);
                                            $holidayMap[$hDt] = $isCollective ? 'collective' : 'holiday';
                                        }
                                    }
                                }"""

if old_php in content:
    content = content.replace(old_php, new_php)
    with open(r"C:\Users\LEGION\Herd\meja-o-app\resources\views\database.blade.php", "w", encoding="utf-8") as f:
        f.write(content)
    print("Fixed cache key retrieval!")
else:
    print("Could not find old PHP code!")
