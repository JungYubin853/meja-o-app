<?php
$content = file_get_contents('C:\Users\LEGION\Herd\meja-o-app\app\Http\Controllers\ReportController.php');

// Replace the entire if ($viewMode === 'habits') block
$pattern = '/if \(\$viewMode === \'habits\'\) \{.*?\}/s';
// Wait, regex might fail if there are nested braces.
