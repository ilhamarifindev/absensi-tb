<?php
$classes = App\Models\Student::select('class_name')->distinct()->pluck('class_name');
foreach($classes as $c) {
    App\Models\ClassRoom::firstOrCreate(['name' => $c]);
}
echo "Done";
