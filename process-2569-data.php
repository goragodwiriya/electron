<?php
/**
 * Process 2569 Election Data
 * แปลงข้อมูลจาก data/2569/ ให้อยู่ในรูปแบบที่ใช้ในโปรเจ็คได้
 */

// อ่านข้อมูลพรรค
$partyOverview = json_decode(file_get_contents(__DIR__.'/data/2569/info_party_overview.json'), true);
$statsParty = json_decode(file_get_contents(__DIR__.'/data/2569/stats_party.json'), true);
$provinceInfo = json_decode(file_get_contents(__DIR__.'/data/2569/info_province.json'), true);

// สร้าง mapping ของพรรค
$partyMap = [];
foreach ($partyOverview as $party) {
    $partyMap[$party['id']] = $party;
}

// ประมวลผลข้อมูลพรรค
$parties = [];
foreach ($statsParty['result_party'] as $partyResult) {
    $partyId = $partyResult['party_id'];

    if (!isset($partyMap[$partyId])) {
        continue;
    }

    $partyInfo = $partyMap[$partyId];

    // คำนวณที่นั่งทั้งหมด
    $totalSeats = $partyResult['first_mp_app_count'] ?? 0;

    // ถ้าไม่มีข้อมูลที่นั่ง ข้ามไป
    if ($totalSeats == 0) {
        continue;
    }

    $parties[] = [
        'id' => 'party_'.$partyId,
        'name' => $partyInfo['name'],
        'nameEn' => $partyInfo['name'], // ใช้ชื่อไทยแทนถ้าไม่มีชื่ออังกฤษ
        'color' => $partyInfo['color'],
        'seats' => $totalSeats,
        'constituencySeats' => $totalSeats, // ส่วนใหญ่เป็นเขต
        'partyListSeats' => 0, // จะคำนวณทีหลัง
        'votes' => number_format($partyResult['party_vote'] / 1000000, 1).' ล้าน',
        'votesRaw' => $partyResult['party_vote'],
        'votePercentage' => number_format($partyResult['party_vote_percent'], 1).'%',
        'seatPercentage' => $partyResult['party_vote_percent'],
        'leader' => '', // ไม่มีข้อมูลในไฟล์
        'policies' => [],
        'status' => 'active',
        'dissolutionDate' => null
    ];
}

// เรียงตามจำนวนที่นั่ง
usort($parties, function ($a, $b) {
    return $b['seats'] - $a['seats'];
});

// สร้างข้อมูลระดับชาติ
$national = [
    'eligibleVoters' => $provinceInfo['total_registered_vote'],
    'actualVoters' => $statsParty['counted_vote_stations'] > 0 ?
    round($provinceInfo['total_registered_vote'] * ($statsParty['percent_count'] / 100)) : 0,
    'turnoutPercentage' => 65.44 // จากข้อมูล
];

// สร้างโครงสร้างข้อมูลสุดท้าย
$electionData2569 = [
    'date' => '9 ก.พ. 2569',
    'national' => $national,
    'parties' => $parties
];

// บันทึกเป็นไฟล์ JSON
$outputPath = __DIR__.'/data/2569-processed.json';
file_put_contents($outputPath, json_encode($electionData2569, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✓ ประมวลผลข้อมูล 2569 เสร็จสิ้น\n";
echo "✓ พบพรรคการเมือง: ".count($parties)." พรรค\n";
echo "✓ พรรคที่ได้ที่นั่งมากที่สุด: ".$parties[0]['name']." (".$parties[0]['seats']." ที่นั่ง)\n";
echo "✓ บันทึกไฟล์: $outputPath\n";

// แสดงรายชื่อพรรคที่ได้ที่นั่ง
echo "\nพรรคที่ได้ที่นั่ง:\n";
foreach (array_slice($parties, 0, 10) as $i => $party) {
    echo($i + 1).". ".$party['name']." - ".$party['seats']." ที่นั่ง (".$party['votePercentage'].")\n";
}
