<?php
/**
 * Process 2569 District Data
 * สร้างไฟล์ districts-2569.json จากข้อมูล stats_cons.json
 */

$statsCons = json_decode(file_get_contents(__DIR__.'/data/2569/stats_cons.json'), true);
$consInfo = json_decode(file_get_contents(__DIR__.'/data/2569/info_constituency.json'), true);
$partyOverview = json_decode(file_get_contents(__DIR__.'/data/2569/info_party_overview.json'), true);

// สร้าง mapping
$partyMap = [];
foreach ($partyOverview as $party) {
    $partyMap[$party['id']] = $party;
}

$consMap = [];
foreach ($consInfo as $cons) {
    $consMap[$cons['cons_id']] = $cons;
}

// ประมวลผลข้อมูลรายจังหวัด
$districts = [];

foreach ($statsCons['result_province'] as $province) {
    $provId = $province['prov_id'];

    if (!isset($province['constituencies'])) {
        continue;
    }

    $provinceDistricts = [];

    foreach ($province['constituencies'] as $cons) {
        $consId = $cons['cons_id'];

        if (!isset($consMap[$consId])) {
            continue;
        }

        $consData = $consMap[$consId];

        // หาผู้ชนะ
        $winner = null;
        if (isset($cons['candidates']) && count($cons['candidates']) > 0) {
            $topCandidate = $cons['candidates'][0];

            if (isset($partyMap[$topCandidate['party_id']])) {
                $winnerParty = $partyMap[$topCandidate['party_id']];
                $winner = [
                    'name' => $topCandidate['mp_app_id'],
                    'party' => $winnerParty['name'],
                    'partyColor' => $winnerParty['color'],
                    'votes' => $topCandidate['mp_app_vote'],
                    'percentage' => $topCandidate['mp_app_vote_percent']
                ];
            }
        }

        $provinceDistricts[] = [
            'id' => $consId,
            'number' => $consData['cons_no'],
            'name' => 'เขต '.$consData['cons_no'],
            'zone' => $consData['zone'] ?? [],
            'winner' => $winner,
            'turnout' => $cons['percent_turn_out'] ?? 0,
            'validVotes' => $cons['valid_votes'] ?? 0
        ];
    }

    if (count($provinceDistricts) > 0) {
        $districts[$provId] = $provinceDistricts;
    }
}

// บันทึกไฟล์
$outputPath = __DIR__.'/data/districts-2569.json';
file_put_contents($outputPath, json_encode($districts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ สร้างไฟล์ districts-2569.json เสร็จสิ้น\n";
echo "✓ จำนวนจังหวัด: ".count($districts)."\n";
echo "✓ บันทึกไฟล์: $outputPath\n";

// แสดงตัวอย่าง
echo "\nตัวอย่างข้อมูล กรุงเทพมหานคร:\n";
if (isset($districts['BKK'])) {
    echo "- จำนวนเขต: ".count($districts['BKK'])."\n";
    if (count($districts['BKK']) > 0) {
        $firstDistrict = $districts['BKK'][0];
        echo "- เขตแรก: ".$firstDistrict['name']."\n";
        if ($firstDistrict['winner']) {
            echo "  ผู้ชนะ: ".$firstDistrict['winner']['party']."\n";
        }
    }
}
