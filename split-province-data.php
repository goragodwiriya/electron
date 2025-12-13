<?php
/**
 * Process Province Results - Simplified for Map
 * สร้างข้อมูลระดับจังหวัดสำหรับแผนที่ (ขนาดเล็ก)
 * และข้อมูลเขตแยกต่างหาก (โหลดเมื่อคลิก)
 */

$dataDir = __DIR__.'/data';

// Load full province results
$fullResults = json_decode(file_get_contents($dataDir.'/province-results.js'), true);
// Remove JS variable declaration
$jsonStr = preg_replace('/^.*?=\s*/s', '', file_get_contents($dataDir.'/province-results.js'));
$jsonStr = rtrim($jsonStr, ";\n");
$fullResults = json_decode($jsonStr, true);

if (!$fullResults) {
    echo "Error: Could not parse province-results.js\n";
    exit(1);
}

// Create simplified map data (just for coloring the map)
$mapData = [];
foreach ($fullResults as $year => $provinces) {
    $mapData[$year] = [];
    foreach ($provinces as $name => $province) {
        $mapData[$year][$province['id']] = [
            'name' => $name,
            'party' => $province['winnerParty'],
            'color' => $province['color'],
            'seats' => $province['totalSeats'] ?? 0,
            'won' => $province['winnerSeats'] ?? 0,
            'votes' => $province['totalVotes'] ?? $province['winnerVotes'] ?? 0
        ];
    }
}

// Generate map-data.js (small file for initial load)
$jsContent = "/**\n * Province Map Data - Simplified\n * Generated: ".date('Y-m-d H:i:s')."\n */\n\n";
$jsContent .= "const provinceMapData = ".json_encode($mapData, JSON_UNESCAPED_UNICODE).";\n";

file_put_contents($dataDir.'/map-data.js', $jsContent);
echo "Created map-data.js: ".number_format(filesize($dataDir.'/map-data.js'))." bytes\n";

// Create separate district files for each year (loaded on demand)
foreach ($fullResults as $year => $provinces) {
    $districtData = [];
    foreach ($provinces as $name => $province) {
        if (!empty($province['districts'])) {
            $districtData[$province['id']] = [
                'name' => $name,
                'districts' => $province['districts']
            ];
        } else if (!empty($province['parties'])) {
            // 2554 - no districts, but has party breakdown
            $districtData[$province['id']] = [
                'name' => $name,
                'parties' => $province['parties']
            ];
        }
    }

    $jsContent = json_encode($districtData, JSON_UNESCAPED_UNICODE);
    file_put_contents($dataDir."/districts-$year.json", $jsContent);
    echo "Created districts-$year.json: ".number_format(filesize($dataDir."/districts-$year.json"))." bytes\n";
}

echo "\nDone!\n";
