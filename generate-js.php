<?php
/**
 * Generate election-data.js from real-election-data.json
 */

$jsonPath = __DIR__.'/data/real-election-data.json';
$data = json_decode(file_get_contents($jsonPath), true);

// Add election dates
$dates = [
    2566 => ['date' => '14 พ.ค. 2566', 'dateEn' => 'May 14, 2023'],
    2562 => ['date' => '24 มี.ค. 2562', 'dateEn' => 'March 24, 2019'],
    2554 => ['date' => '3 ก.ค. 2554', 'dateEn' => 'July 3, 2011']
];

foreach ($data as $year => &$yearData) {
    $yearData['date'] = $dates[$year]['date'] ?? '';
    $yearData['dateEn'] = $dates[$year]['dateEn'] ?? '';
}

// Generate JavaScript
$js = "/**
 * Thai Election Data - Extracted from ECT Official XLS Files
 * ข้อมูลจริงจาก กกต. (คณะกรรมการการเลือกตั้ง)
 * Generated: ".date('Y-m-d H:i:s')."
 *
 * หมายเหตุ: ข้อมูลพรรคดึงจากไฟล์ XLS ของ กกต. โดยตรง
 */

const electionDataFromXLS = ".json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).";

// Export for use in hub dashboard
if (typeof module !== 'undefined' && module.exports) {
    module.exports = electionDataFromXLS;
}
";

$jsPath = __DIR__.'/data/election-data.js';
file_put_contents($jsPath, $js);
echo "Created: $jsPath\n";

// Show summary
foreach ($data as $year => $d) {
    $partyCount = count($d['parties'] ?? []);
    $topInfo = '';
    if (!empty($d['parties'])) {
        $topParty = $d['parties'][0];
        if (isset($topParty['votes']) && $topParty['votes'] > 0) {
            $topInfo = '(Top: '.number_format($topParty['votes']).' votes)';
        } elseif (isset($topParty['seats']) && $topParty['seats'] > 0) {
            $topInfo = '(Top: '.$topParty['seats'].' seats)';
        }
    }
    echo "Year $year: $partyCount parties $topInfo\n";
}
