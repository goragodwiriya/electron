<?php
/**
 * Process 2569 Map Data
 * สร้างข้อมูลแผนที่จาก stats_cons.json
 */

$statsCons = json_decode(file_get_contents(__DIR__.'/data/2569/stats_cons.json'), true);
$provinceInfo = json_decode(file_get_contents(__DIR__.'/data/2569/info_province.json'), true);
$partyOverview = json_decode(file_get_contents(__DIR__.'/data/2569/info_party_overview.json'), true);

// สร้าง mapping
$partyMap = [];
foreach ($partyOverview as $party) {
    $partyMap[$party['id']] = $party;
}

// Mapping ชื่อจังหวัดภาษาอังกฤษ
$provinceNameMap = [
    'BKK' => 'bangkok',
    'SPK' => 'samut-prakan',
    'NBI' => 'nonthaburi',
    'PTE' => 'pathum-thani',
    'AYA' => 'ayutthaya',
    'ATG' => 'ang-thong',
    'LRI' => 'lopburi',
    'SBR' => 'singburi',
    'CNT' => 'chainat',
    'SRI' => 'saraburi',
    'CBI' => 'chonburi',
    'RYG' => 'rayong',
    'CTI' => 'chanthaburi',
    'TRT' => 'trat',
    'CCO' => 'chachoengsao',
    'PRI' => 'prachinburi',
    'NYK' => 'nakhon-nayok',
    'SKW' => 'sa-kaeo',
    'NMA' => 'nakhon-ratchasima',
    'BRM' => 'buriram',
    'SRN' => 'surin',
    'SSK' => 'sisaket',
    'UBN' => 'ubon-ratchathani',
    'YST' => 'yasothon',
    'CPM' => 'chaiyaphum',
    'ACR' => 'amnat-charoen',
    'BKN' => 'bueng-kan',
    'NBP' => 'nong-bua-lam-phu',
    'KKN' => 'khon-kaen',
    'UDN' => 'udon-thani',
    'LEI' => 'loei',
    'NKI' => 'nong-khai',
    'MKM' => 'maha-sarakham',
    'RET' => 'roi-et',
    'KSN' => 'kalasin',
    'SNK' => 'sakon-nakhon',
    'NPM' => 'nakhon-phanom',
    'MDH' => 'mukdahan',
    'CMI' => 'chiang-mai',
    'LPN' => 'lamphun',
    'LPG' => 'lampang',
    'UTT' => 'uttaradit',
    'PRE' => 'phrae',
    'NAN' => 'nan',
    'PYO' => 'phayao',
    'CRI' => 'chiang-rai',
    'MSN' => 'mae-hong-son',
    'NSN' => 'nakhon-sawan',
    'UTI' => 'uthai-thani',
    'KPT' => 'kamphaeng-phet',
    'TAK' => 'tak',
    'STI' => 'sukhothai',
    'PLK' => 'phitsanulok',
    'PCT' => 'phichit',
    'PNB' => 'phetchabun',
    'RBR' => 'ratchaburi',
    'KRI' => 'kanchanaburi',
    'SPB' => 'suphan-buri',
    'NPT' => 'nakhon-pathom',
    'SKN' => 'samut-sakhon',
    'SKM' => 'samut-songkhram',
    'PBI' => 'phetchaburi',
    'PKN' => 'prachuap-khiri-khan',
    'NST' => 'nakhon-si-thammarat',
    'KBI' => 'krabi',
    'PNA' => 'phang-nga',
    'PKT' => 'phuket',
    'SNI' => 'surat-thani',
    'RNG' => 'ranong',
    'CPN' => 'chumphon',
    'SKA' => 'songkhla',
    'STN' => 'satun',
    'TRG' => 'trang',
    'PLG' => 'phatthalung',
    'PTN' => 'pattani',
    'YLA' => 'yala',
    'NWT' => 'narathiwat'
];

// สร้าง mapping จังหวัด
$provinceMap = [];
foreach ($provinceInfo['province'] as $prov) {
    $provinceMap[$prov['prov_id']] = $prov;
}

// ประมวลผลข้อมูลแผนที่
$mapData = [];

foreach ($statsCons['result_province'] as $province) {
    $provId = $province['prov_id'];

    if (!isset($provinceMap[$provId]) || !isset($provinceNameMap[$provId])) {
        continue;
    }

    $provInfo = $provinceMap[$provId];
    $provNameEn = $provinceNameMap[$provId];

    // หาพรรคที่ชนะมากที่สุด
    $topParty = null;
    $topPartyVotes = 0;
    $topPartySeats = 0;

    if (isset($province['result_party'])) {
        foreach ($province['result_party'] as $partyResult) {
            $partyId = $partyResult['party_id'];
            $seats = $partyResult['first_mp_app_count'] ?? 0;

            if ($seats > $topPartySeats) {
                $topPartySeats = $seats;
                $topPartyVotes = $partyResult['party_cons_votes'];

                if (isset($partyMap[$partyId])) {
                    $topParty = $partyMap[$partyId];
                }
            }
        }
    }

    if ($topParty) {
        $mapData[$provNameEn] = [
            'name' => $provInfo['province'],
            'party' => $topParty['name'],
            'color' => $topParty['color'],
            'seats' => count($province['constituencies'] ?? []),
            'won' => $topPartySeats,
            'votes' => $topPartyVotes
        ];
    }
}

// อ่านไฟล์ map-data.js เดิม
$mapDataJs = file_get_contents(__DIR__.'/data/map-data.js');

// แยกข้อมูล
preg_match('/const provinceMapData = ({.*?});/s', $mapDataJs, $matches);
$existingData = json_decode($matches[1], true);

// เพิ่มข้อมูลปี 2569
$existingData['2569'] = $mapData;

// สร้างไฟล์ใหม่
$newMapDataJs = "/**
 * Province Map Data - Simplified
 * Generated: ".date('Y-m-d H:i:s')."
 * Updated: Added 2569 data
 */

const provinceMapData = ".json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).";
";

file_put_contents(__DIR__.'/data/map-data.js', $newMapDataJs);

echo "✓ อัพเดทไฟล์ map-data.js เสร็จสิ้น\n";
echo "✓ จำนวนจังหวัดปี 2569: ".count($mapData)."\n";

// แสดงตัวอย่าง
echo "\nตัวอย่างข้อมูล:\n";
$sample = array_slice($mapData, 0, 5);
foreach ($sample as $key => $data) {
    echo "- ".$data['name'].": ".$data['party']." (".$data['won']."/".$data['seats']." เขต)\n";
}
