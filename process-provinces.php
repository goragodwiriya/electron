<?php
/**
 * Process Province & District Results for Interactive Map
 * สร้างข้อมูลระดับจังหวัดและเขตสำหรับแผนที่ interactive
 */

// Province name mapping (Thai to ID)
$provinceIds = [
    'กรุงเทพมหานคร' => 'bangkok',
    'สมุทรปราการ' => 'samut-prakan',
    'นนทบุรี' => 'nonthaburi',
    'ปทุมธานี' => 'pathum-thani',
    'พระนครศรีอยุธยา' => 'ayutthaya',
    'อ่างทอง' => 'ang-thong',
    'ลพบุรี' => 'lopburi',
    'สิงห์บุรี' => 'singburi',
    'ชัยนาท' => 'chainat',
    'สระบุรี' => 'saraburi',
    'ชลบุรี' => 'chonburi',
    'ระยอง' => 'rayong',
    'จันทบุรี' => 'chanthaburi',
    'ตราด' => 'trat',
    'ฉะเชิงเทรา' => 'chachoengsao',
    'ปราจีนบุรี' => 'prachinburi',
    'นครนายก' => 'nakhon-nayok',
    'สระแก้ว' => 'sa-kaeo',
    'นครราชสีมา' => 'nakhon-ratchasima',
    'บุรีรัมย์' => 'buriram',
    'สุรินทร์' => 'surin',
    'ศรีสะเกษ' => 'sisaket',
    'อุบลราชธานี' => 'ubon-ratchathani',
    'ยโสธร' => 'yasothon',
    'ชัยภูมิ' => 'chaiyaphum',
    'อำนาจเจริญ' => 'amnat-charoen',
    'หนองบัวลำภู' => 'nong-bua-lam-phu',
    'ขอนแก่น' => 'khon-kaen',
    'อุดรธานี' => 'udon-thani',
    'เลย' => 'loei',
    'หนองคาย' => 'nong-khai',
    'มหาสารคาม' => 'maha-sarakham',
    'ร้อยเอ็ด' => 'roi-et',
    'กาฬสินธุ์' => 'kalasin',
    'สกลนคร' => 'sakon-nakhon',
    'นครพนม' => 'nakhon-phanom',
    'มุกดาหาร' => 'mukdahan',
    'เชียงใหม่' => 'chiang-mai',
    'ลำพูน' => 'lamphun',
    'ลำปาง' => 'lampang',
    'อุตรดิตถ์' => 'uttaradit',
    'แพร่' => 'phrae',
    'น่าน' => 'nan',
    'พะเยา' => 'phayao',
    'เชียงราย' => 'chiang-rai',
    'แม่ฮ่องสอน' => 'mae-hong-son',
    'นครสวรรค์' => 'nakhon-sawan',
    'อุทัยธานี' => 'uthai-thani',
    'กำแพงเพชร' => 'kamphaeng-phet',
    'ตาก' => 'tak',
    'สุโขทัย' => 'sukhothai',
    'พิษณุโลก' => 'phitsanulok',
    'พิจิตร' => 'phichit',
    'เพชรบูรณ์' => 'phetchabun',
    'ราชบุรี' => 'ratchaburi',
    'กาญจนบุรี' => 'kanchanaburi',
    'สุพรรณบุรี' => 'suphan-buri',
    'นครปฐม' => 'nakhon-pathom',
    'สมุทรสาคร' => 'samut-sakhon',
    'สมุทรสงคราม' => 'samut-songkhram',
    'เพชรบุรี' => 'phetchaburi',
    'ประจวบคีรีขันธ์' => 'prachuap-khiri-khan',
    'นครศรีธรรมราช' => 'nakhon-si-thammarat',
    'กระบี่' => 'krabi',
    'พังงา' => 'phang-nga',
    'ภูเก็ต' => 'phuket',
    'สุราษฎร์ธานี' => 'surat-thani',
    'ระนอง' => 'ranong',
    'ชุมพร' => 'chumphon',
    'สงขลา' => 'songkhla',
    'สตูล' => 'satun',
    'ตรัง' => 'trang',
    'พัทลุง' => 'phatthalung',
    'ปัตตานี' => 'pattani',
    'ยะลา' => 'yala',
    'นราธิวาส' => 'narathiwat',
    'บึงกาฬ' => 'bueng-kan'
];

// Party colors
$partyColors = [
    'ก้าวไกล' => '#F47933',
    'เพื่อไทย' => '#D61B23',
    'ภูมิใจไทย' => '#15235E',
    'พลังประชารัฐ' => '#1D4ED8',
    'รวมไทยสร้างชาติ' => '#0369A1',
    'ประชาธิปัตย์' => '#00A5E3',
    'ชาติไทยพัฒนา' => '#2D4356',
    'ประชาชาติ' => '#065F46',
    'ไทยสร้างไทย' => '#7C3AED',
    'เสรีรวมไทย' => '#F59E0B',
    'อนาคตใหม่' => '#F47933',
    'ชาติพัฒนากล้า' => '#059669'
];

/**
 * Get party color
 */
function getPartyColor($partyName, $partyColors)
{
    foreach ($partyColors as $name => $color) {
        if (mb_strpos($partyName, $name) !== false) {
            return $color;
        }
    }
    return '#'.substr(md5($partyName), 0, 6);
}

/**
 * Process 2566/2562 JSON (candidate results to province/district summary)
 */
function processConstituencyResults($jsonFile, $provinceIds, $partyColors)
{
    $json = json_decode(file_get_contents($jsonFile), true);
    $data = $json['data'];

    $provinces = [];
    $currentProvince = '';
    $currentDistrict = '';
    $districtResults = [];

    foreach ($data as $row) {
        // Skip header rows
        if (empty($row['E']) || $row['E'] === 'พรรค' || mb_strpos($row['E'], 'ผลคะแนน') !== false) {
            continue;
        }

        $province = trim($row['A'] ?? '');
        $district = trim($row['B'] ?? '');
        $candidate = trim($row['D'] ?? '');
        $party = trim($row['E'] ?? '');
        $votes = intval(str_replace(',', '', $row['F'] ?? 0));

        if (!empty($province)) {
            // Save previous province's last district
            if ($currentProvince && $currentDistrict && !empty($districtResults)) {
                saveDistrictWinner($provinces, $currentProvince, $currentDistrict, $districtResults, $provinceIds, $partyColors);
            }
            $currentProvince = $province;
            $currentDistrict = '';
            $districtResults = [];
        }

        if (!empty($district) && $district !== $currentDistrict) {
            // Save previous district
            if ($currentDistrict && !empty($districtResults)) {
                saveDistrictWinner($provinces, $currentProvince, $currentDistrict, $districtResults, $provinceIds, $partyColors);
            }
            $currentDistrict = $district;
            $districtResults = [];
        }

        if ($votes > 0 && !empty($party)) {
            $districtResults[] = [
                'candidate' => $candidate,
                'party' => $party,
                'votes' => $votes
            ];
        }
    }

    // Save last district
    if ($currentProvince && $currentDistrict && !empty($districtResults)) {
        saveDistrictWinner($provinces, $currentProvince, $currentDistrict, $districtResults, $provinceIds, $partyColors);
    }

    // Calculate province winners
    foreach ($provinces as $name => &$province) {
        $partyWins = [];
        $totalVotes = 0;

        foreach ($province['districts'] as $district) {
            $party = $district['winnerParty'];
            if (!isset($partyWins[$party])) {
                $partyWins[$party] = 0;
            }
            $partyWins[$party]++;
            $totalVotes += $district['winnerVotes'];
        }

        // Find party with most district wins
        arsort($partyWins);
        $winnerParty = array_key_first($partyWins);

        $province['winnerParty'] = $winnerParty;
        $province['winnerSeats'] = $partyWins[$winnerParty];
        $province['totalSeats'] = count($province['districts']);
        $province['totalVotes'] = $totalVotes;
        $province['color'] = getPartyColor($winnerParty, $partyColors);
    }
    unset($province);

    return $provinces;
}

/**
 * Save district winner to provinces array
 */
function saveDistrictWinner(&$provinces, $provinceName, $district, $results, $provinceIds, $partyColors)
{
    if (!isset($provinces[$provinceName])) {
        $provinces[$provinceName] = [
            'id' => $provinceIds[$provinceName] ?? strtolower(str_replace(' ', '-', $provinceName)),
            'name' => $provinceName,
            'districts' => []
        ];
    }

    // Find winner (highest votes)
    usort($results, function ($a, $b) {
        return $b['votes'] - $a['votes'];
    });

    $winner = $results[0];

    $provinces[$provinceName]['districts'][] = [
        'district' => $district,
        'winner' => $winner['candidate'],
        'winnerParty' => $winner['party'],
        'winnerVotes' => $winner['votes'],
        'color' => getPartyColor($winner['party'], $partyColors),
        'candidates' => array_slice($results, 0, 5) // Top 5 candidates
    ];
}

/**
 * Process 2554 JSON (different structure - party votes by province)
 */
function process2554Results($jsonFile, $provinceIds, $partyColors)
{
    $json = json_decode(file_get_contents($jsonFile), true);
    $data = $json['data'];

    // Get province columns from header
    $provinceColumns = [];
    foreach ($data as $row) {
        if (isset($row['A']) && $row['A'] === 'หมายเลข') {
            foreach ($row as $col => $value) {
                if ($col !== 'A' && $col !== 'B' && $col !== '_row' && !empty($value)) {
                    $provinceColumns[$col] = $value;
                }
            }
            break;
        }
    }

    // Get party votes for each province
    $partyVotesByProvince = [];
    foreach ($provinceColumns as $col => $provinceName) {
        $partyVotesByProvince[$provinceName] = [];
    }

    foreach ($data as $row) {
        if (!isset($row['A']) || !is_numeric($row['A'])) {
            continue;
        }

        $partyName = trim($row['B'] ?? '');
        if (empty($partyName) || mb_strpos($partyName, 'รวม') !== false) {
            continue;
        }

        foreach ($provinceColumns as $col => $provinceName) {
            $votes = intval(str_replace(',', '', $row[$col] ?? 0));
            if ($votes > 0) {
                $partyVotesByProvince[$provinceName][] = [
                    'party' => $partyName,
                    'votes' => $votes
                ];
            }
        }
    }

    // Find winner for each province
    $provinces = [];
    foreach ($partyVotesByProvince as $provinceName => $parties) {
        if (empty($parties)) {
            continue;
        }

        usort($parties, function ($a, $b) {
            return $b['votes'] - $a['votes'];
        });

        $winner = $parties[0];
        $totalVotes = array_sum(array_column($parties, 'votes'));

        $provinces[$provinceName] = [
            'id' => $provinceIds[$provinceName] ?? strtolower(str_replace(' ', '-', $provinceName)),
            'name' => $provinceName,
            'winnerParty' => $winner['party'],
            'winnerVotes' => $winner['votes'],
            'totalVotes' => $totalVotes,
            'color' => getPartyColor($winner['party'], $partyColors),
            'districts' => [], // No district data for 2554
            'parties' => array_slice($parties, 0, 5) // Top 5 parties
        ];
    }

    return $provinces;
}

// Main execution
$dataDir = __DIR__.'/data';

echo "=== Processing Province Results ===\n\n";

$results = [];

// 2566
echo "Processing 2566...\n";
$results[2566] = processConstituencyResults($dataDir.'/2566.json', $provinceIds, $partyColors);
echo "  Found ".count($results[2566])." provinces\n";

// 2562
echo "Processing 2562...\n";
$results[2562] = processConstituencyResults($dataDir.'/2562.json', $provinceIds, $partyColors);
echo "  Found ".count($results[2562])." provinces\n";

// 2554
echo "Processing 2554...\n";
$results[2554] = process2554Results($dataDir.'/2554.json', $provinceIds, $partyColors);
echo "  Found ".count($results[2554])." provinces\n";

// Generate JavaScript
$jsContent = "/**\n * Province Election Results - Auto-generated\n * Generated: ".date('Y-m-d H:i:s')."\n */\n\n";
$jsContent .= "const provinceResults = ".json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).";\n";

$outputPath = $dataDir.'/province-results.js';
file_put_contents($outputPath, $jsContent);

echo "\n=== Complete ===\n";
echo "Generated: $outputPath\n";
echo "File size: ".number_format(filesize($outputPath))." bytes\n";

// Show sample
foreach ($results as $year => $provinces) {
    echo "\n$year:\n";
    $sample = array_slice($provinces, 0, 3, true);
    foreach ($sample as $name => $p) {
        echo "  - $name: {$p['winnerParty']}";
        if (isset($p['totalSeats'])) {
            echo " ({$p['winnerSeats']}/{$p['totalSeats']} เขต)";
        }
        echo "\n";
    }
}
