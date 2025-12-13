<?php
/**
 * Normalize Election Data
 * แปลง JSON ทั้ง 3 ปี (2554, 2562, 2566) ให้มีโครงสร้างเดียวกัน
 * และสร้าง election-data.js ที่ใช้งานได้ทันที
 */

// ข้อมูลสี พรรคการเมือง
$partyColors = [
    // 2566
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
    'ชาติพัฒนากล้า' => '#059669',
    // 2562
    'อนาคตใหม่' => '#F47933',
    'รวมพลังประชาชาติไทย' => '#9E1B32',
    'เศรษฐกิจใหม่' => '#4A5568',
    // 2554
    'ชาติพัฒนาเพื่อแผ่นดิน' => '#059669',
    'รักประเทศไทย' => '#C41E3A',
    'ชาติไทย' => '#8B4513',
    'พลังชล' => '#0077BE',
    'มาตุภูมิ' => '#228B22'
];

// ข้อมูลชื่อภาษาอังกฤษ
$partyNamesEn = [
    'ก้าวไกล' => 'Move Forward',
    'เพื่อไทย' => 'Pheu Thai',
    'ภูมิใจไทย' => 'Bhumjaithai',
    'พลังประชารัฐ' => 'Palang Pracharath',
    'รวมไทยสร้างชาติ' => 'United Thai Nation',
    'ประชาธิปัตย์' => 'Democrat',
    'ชาติไทยพัฒนา' => 'Chartthaipattana',
    'ประชาชาติ' => 'Prachachart',
    'ไทยสร้างไทย' => 'Thai Sang Thai',
    'เสรีรวมไทย' => 'Seri Ruam Thai',
    'ชาติพัฒนากล้า' => 'Chart Pattana Kla',
    'อนาคตใหม่' => 'Future Forward',
    'รวมพลังประชาชาติไทย' => 'Action Coalition Thailand',
    'เศรษฐกิจใหม่' => 'New Economics',
    'ชาติพัฒนาเพื่อแผ่นดิน' => 'Chart Pattana Puea Pandin',
    'รักประเทศไทย' => 'Rak Prathet Thai',
    'พลังชล' => 'Palang Chon',
    'มาตุภูมิ' => 'Matubhum'
];

// ข้อมูลผู้นำพรรค (เฉพาะปี 2566)
$partyLeaders2566 = [
    'ก้าวไกล' => 'พิธา ลิ้มเจริญรัตน์',
    'เพื่อไทย' => 'แพทองธาร ชินวัตร',
    'ภูมิใจไทย' => 'อนุทิน ชาญวีรกูล',
    'พลังประชารัฐ' => 'ประวิตร วงษ์สุวรรณ',
    'รวมไทยสร้างชาติ' => 'ประยุทธ์ จันทร์โอชา',
    'ประชาธิปัตย์' => 'จุรินทร์ ลักษณวิศิษฏ์',
    'ชาติไทยพัฒนา' => 'วราวุธ ศิลปอาชา',
    'ประชาชาติ' => 'วันมูหะมัดนอร์ มะทา',
    'ไทยสร้างไทย' => 'คุณหญิงสุดารัตน์ เกยุราพันธุ์',
    'เสรีรวมไทย' => 'พล.ต.อ.เสรีพิศุทธ์ เตมียาเวส',
    'ชาติพัฒนากล้า' => 'กรณ์ จาติกวณิช'
];

// ข้อมูลผู้นำพรรคปี 2562
$partyLeaders2562 = [
    'อนาคตใหม่' => 'ธนาธร จึงรุ่งเรืองกิจ',
    'พลังประชารัฐ' => 'อุตตม สาวนายน',
    'เพื่อไทย' => 'สุดารัตน์ เกยุราพันธุ์',
    'ประชาธิปัตย์' => 'อภิสิทธิ์ เวชชาชีวะ',
    'ภูมิใจไทย' => 'อนุทิน ชาญวีรกูล',
    'ชาติไทยพัฒนา' => 'กัญจนา ศิลปอาชา',
    'เสรีรวมไทย' => 'พล.ต.อ.เสรีพิศุทธ์ เตมียาเวส',
    'ประชาชาติ' => 'วันมูหะมัดนอร์ มะทา',
    'เศรษฐกิจใหม่' => 'มิ่งขวัญ แสงสุวรรณ์',
    'รวมพลังประชาชาติไทย' => 'สุเทพ เทือกสุบรรณ'
];

// ข้อมูลผู้นำพรรคปี 2554
$partyLeaders2554 = [
    'เพื่อไทย' => 'ยิ่งลักษณ์ ชินวัตร',
    'ประชาธิปัตย์' => 'อภิสิทธิ์ เวชชาชีวะ',
    'ภูมิใจไทย' => 'ชวรัตน์ ชาญวีรกูล',
    'ชาติไทยพัฒนา' => 'ชุมพล ศิลปอาชา',
    'ชาติพัฒนาเพื่อแผ่นดิน' => 'สุวัจน์ ลิปตพัลลภ',
    'พลังชล' => 'สนธยา คุณปลื้ม',
    'รักประเทศไทย' => 'ชูวิทย์ กมลวิศิษฎ์',
    'มาตุภูมิ' => 'พล.อ.สนธิ บุญยรัตกลิน'
];

// ข้อมูลนโยบายหลักปี 2566
$partyPolicies2566 = [
    'ก้าวไกล' => ['ร่างรัฐธรรมนูญใหม่', 'ยกเลิกเกณฑ์ทหาร', 'ค่าแรงขั้นต่ำ 450 บาท'],
    'เพื่อไทย' => ['เงินดิจิทัล 10,000 บาท', 'ค่าแรง 600 บาท', 'รถไฟฟ้า 20 บาทตลอดสาย'],
    'ภูมิใจไทย' => ['พักหนี้ 3 ปี', 'นักท่องเที่ยว 80 ล้านคน', 'กัญชาทางการแพทย์'],
    'พลังประชารัฐ' => ['เกษตรประชารัฐ', 'ดูแลผู้สูงอายุ', 'สานต่อโครงการประชารัฐ'],
    'รวมไทยสร้างชาติ' => ['สร้างชาติ สร้างงาน สร้างรายได้', 'ดูแลผู้มีรายได้น้อย', 'สานต่อโครงการรัฐบาลเดิม'],
    'ประชาธิปัตย์' => ['ประกันรายได้เกษตรกร', 'สวัสดิการผู้สูงอายุ', 'กระจายอำนาจสู่ท้องถิ่น'],
    'ชาติไทยพัฒนา' => ['พัฒนาภาคเกษตร', 'ท่องเที่ยวเชิงวัฒนธรรม', 'แก้ปัญหาน้ำท่วม'],
    'ประชาชาติ' => ['แก้ปัญหาชายแดนใต้', 'สิทธิมนุษยชน', 'กระจายอำนาจ'],
    'ไทยสร้างไทย' => ['แก้หนี้ครัวเรือน', 'พัฒนา SME', 'เศรษฐกิจสร้างสรรค์'],
    'เสรีรวมไทย' => ['ต่อต้านทุจริต', 'ปฏิรูปตำรวจ', 'ความยุติธรรม'],
    'ชาติพัฒนากล้า' => ['เศรษฐกิจดิจิทัล', 'การศึกษา 4.0', 'พลังงานสะอาด']
];

// ข้อมูลนโยบายหลักปี 2562
$partyPolicies2562 = [
    'อนาคตใหม่' => ['รัฐสวัสดิการถ้วนหน้า', 'แก้ไข รธน. 60', 'ลดงบกลาโหม'],
    'พลังประชารัฐ' => ['บัตรประชารัฐ', 'พักหนี้กองทุนหมู่บ้าน', 'เกษตรประชารัฐ'],
    'เพื่อไทย' => ['แก้หนี้สร้างรายได้', 'ท่องเที่ยว 50 ล้านคน', 'กระจายอำนาจ'],
    'ประชาธิปัตย์' => ['ประกันรายได้เกษตรกร', 'เบี้ยผู้สูงอายุ', 'ลดค่าครองชีพ'],
    'ภูมิใจไทย' => ['กัญชาเสรี', 'ท่องเที่ยวเชิงสุขภาพ', 'พัฒนาเกษตร'],
    'เสรีรวมไทย' => ['ต่อต้านทุจริต', 'ปฏิรูปตำรวจ', 'ความยุติธรรม'],
    'ชาติไทยพัฒนา' => ['พัฒนาภาคเกษตร', 'แก้ปัญหาน้ำ', 'ท่องเที่ยว'],
    'ประชาชาติ' => ['แก้ปัญหาชายแดนใต้', 'สิทธิมนุษยชน', 'การศึกษา'],
    'เศรษฐกิจใหม่' => ['ปฏิรูปเศรษฐกิจ', 'ลดความเหลื่อมล้ำ', 'พัฒนา SME'],
    'รวมพลังประชาชาติไทย' => ['ปกป้องสถาบัน', 'ปฏิรูปการเมือง', 'ต่อต้านทุจริต']
];

// ข้อมูลนโยบายหลักปี 2554
$partyPolicies2554 = [
    'เพื่อไทย' => ['ค่าแรงขั้นต่ำ 300 บาท', 'รถคันแรก', 'บ้านหลังแรก'],
    'ประชาธิปัตย์' => ['เบี้ยผู้สูงอายุ', 'กองทุนหมู่บ้าน', 'ประกันรายได้เกษตรกร'],
    'ภูมิใจไทย' => ['พัฒนาการเกษตร', 'ท่องเที่ยวเชิงสุขภาพ', 'กระจายอำนาจ'],
    'ชาติไทยพัฒนา' => ['พัฒนาภาคเกษตร', 'แก้ปัญหาน้ำท่วม', 'ท่องเที่ยว'],
    'ชาติพัฒนาเพื่อแผ่นดิน' => ['พัฒนาเศรษฐกิจ', 'แก้ปัญหาความยากจน', 'สร้างงาน'],
    'พลังชล' => ['พัฒนาภาคตะวันออก', 'ท่องเที่ยว', 'อุตสาหกรรม'],
    'รักประเทศไทย' => ['ต่อต้านทุจริต', 'ปฏิรูปการเมือง', 'ความยุติธรรม'],
    'มาตุภูมิ' => ['แก้ปัญหาชายแดนใต้', 'ความมั่นคง', 'พัฒนาเศรษฐกิจ']
];

// ข้อมูลสถานะพรรค
$partyStatus = [
    'ก้าวไกล' => ['status' => 'dissolved', 'dissolutionDate' => '7 ส.ค. 2567'],
    'อนาคตใหม่' => ['status' => 'dissolved', 'dissolutionDate' => '21 ก.พ. 2563']
];

// ข้อมูลที่นั่ง (จากข้อมูลจริง กกต.)
$seatData = [
    2566 => [
        'ก้าวไกล' => ['constituency' => 113, 'partyList' => 38],
        'เพื่อไทย' => ['constituency' => 112, 'partyList' => 29],
        'ภูมิใจไทย' => ['constituency' => 68, 'partyList' => 3],
        'พลังประชารัฐ' => ['constituency' => 39, 'partyList' => 1],
        'รวมไทยสร้างชาติ' => ['constituency' => 23, 'partyList' => 13],
        'ประชาธิปัตย์' => ['constituency' => 22, 'partyList' => 3],
        'ชาติไทยพัฒนา' => ['constituency' => 9, 'partyList' => 1],
        'ประชาชาติ' => ['constituency' => 7, 'partyList' => 2],
        'ไทยสร้างไทย' => ['constituency' => 4, 'partyList' => 2],
        'เสรีรวมไทย' => ['constituency' => 1, 'partyList' => 0],
        'ชาติพัฒนากล้า' => ['constituency' => 2, 'partyList' => 0]
    ],
    2562 => [
        'พลังประชารัฐ' => ['constituency' => 97, 'partyList' => 19],
        'เพื่อไทย' => ['constituency' => 136, 'partyList' => 0],
        'อนาคตใหม่' => ['constituency' => 30, 'partyList' => 50],
        'ประชาธิปัตย์' => ['constituency' => 33, 'partyList' => 19],
        'ภูมิใจไทย' => ['constituency' => 39, 'partyList' => 12],
        'เสรีรวมไทย' => ['constituency' => 0, 'partyList' => 10],
        'ชาติไทยพัฒนา' => ['constituency' => 6, 'partyList' => 4],
        'เศรษฐกิจใหม่' => ['constituency' => 0, 'partyList' => 6],
        'ประชาชาติ' => ['constituency' => 6, 'partyList' => 1],
        'รวมพลังประชาชาติไทย' => ['constituency' => 1, 'partyList' => 4]
    ],
    2554 => [
        'เพื่อไทย' => ['constituency' => 204, 'partyList' => 61],
        'ประชาธิปัตย์' => ['constituency' => 115, 'partyList' => 44],
        'ภูมิใจไทย' => ['constituency' => 29, 'partyList' => 5],
        'ชาติไทยพัฒนา' => ['constituency' => 15, 'partyList' => 4],
        'ชาติพัฒนาเพื่อแผ่นดิน' => ['constituency' => 7, 'partyList' => 0],
        'รักประเทศไทย' => ['constituency' => 4, 'partyList' => 0],
        'มาตุภูมิ' => ['constituency' => 2, 'partyList' => 0],
        'พลังชล' => ['constituency' => 7, 'partyList' => 0]
    ]
];

// ข้อมูลวันเลือกตั้ง
$electionDates = [
    2566 => '14 พ.ค. 2566',
    2562 => '24 มี.ค. 2562',
    2554 => '3 ก.ค. 2554'
];

// ข้อมูลสถิติระดับชาติ
$nationalStats = [
    2566 => [
        'eligibleVoters' => 52322824,
        'actualVoters' => 39589071,
        'turnoutPercentage' => 75.71,
        'spoiledBallots' => 1377358,
        'noVote' => 1008077
    ],
    2562 => [
        'eligibleVoters' => 51427830,
        'actualVoters' => 38268375,
        'turnoutPercentage' => 74.69
    ],
    2554 => [
        'eligibleVoters' => 46939549,
        'actualVoters' => 35203730,
        'turnoutPercentage' => 75.03
    ]
];

/**
 * Get party color
 */
function getPartyColor($partyName, $partyColors)
{
    $clean = trim(preg_replace('/^พรรค/', '', $partyName));
    foreach ($partyColors as $name => $color) {
        if (mb_strpos($partyName, $name) !== false || mb_strpos($clean, $name) !== false) {
            return $color;
        }
    }
    return '#'.substr(md5($partyName), 0, 6);
}

/**
 * Get party name in English
 */
function getPartyNameEn($partyName, $partyNamesEn)
{
    $clean = trim(preg_replace('/^พรรค/', '', $partyName));
    foreach ($partyNamesEn as $name => $nameEn) {
        if (mb_strpos($partyName, $name) !== false || mb_strpos($clean, $name) !== false) {
            return $nameEn;
        }
    }
    return '';
}

/**
 * Get party ID from name
 */
function getPartyId($partyName)
{
    $mapping = [
        'ก้าวไกล' => 'mfp',
        'เพื่อไทย' => 'ptp',
        'ภูมิใจไทย' => 'bjt',
        'พลังประชารัฐ' => 'pprp',
        'รวมไทยสร้างชาติ' => 'utn',
        'ประชาธิปัตย์' => 'dp',
        'ชาติไทยพัฒนา' => 'ctp',
        'ประชาชาติ' => 'prachachat',
        'ไทยสร้างไทย' => 'tst',
        'เสรีรวมไทย' => 'srt',
        'ชาติพัฒนากล้า' => 'cpk',
        'อนาคตใหม่' => 'ffp',
        'ชาติพัฒนาเพื่อแผ่นดิน' => 'cdpp',
        'รักประเทศไทย' => 'rpt',
        'พลังชล' => 'pc',
        'มาตุภูมิ' => 'matubhum'
    ];

    foreach ($mapping as $name => $id) {
        if (mb_strpos($partyName, $name) !== false) {
            return $id;
        }
    }
    return 'party_'.substr(md5($partyName), 0, 6);
}

/**
 * Process 2566/2562 JSON (candidate-level data to party summary)
 */
function processConstituencyData($jsonFile, $year, $seatData, $partyColors, $partyNamesEn, $partyLeaders, $partyPolicies, $partyStatus)
{
    $json = json_decode(file_get_contents($jsonFile), true);
    $data = $json['data'];

    // รวมคะแนนตามพรรค
    $partyVotes = [];
    $partyWins = [];

    $currentProvince = '';
    $currentDistrict = '';

    foreach ($data as $row) {
        // ข้าม header rows
        if (empty($row['E']) || $row['E'] === 'พรรค' || mb_strpos($row['E'], 'ผลคะแนน') !== false) {
            continue;
        }

        $province = trim($row['A'] ?? '');
        $district = trim($row['B'] ?? '');
        $partyName = trim($row['E'] ?? '');
        $votes = intval(str_replace(',', '', $row['F'] ?? 0));

        if (!empty($province)) {
            $currentProvince = $province;
        }
        if (!empty($district)) {
            $currentDistrict = $district;
        }

        if (empty($partyName) || $votes <= 0) {
            continue;
        }

        // รวมคะแนน
        if (!isset($partyVotes[$partyName])) {
            $partyVotes[$partyName] = 0;
        }
        $partyVotes[$partyName] += $votes;

        // นับผู้ชนะแต่ละเขต (คนที่ได้คะแนนสูงสุดในเขต)
        $key = "{$currentProvince}_{$currentDistrict}";
        if (!isset($partyWins[$key]) || $votes > $partyWins[$key]['votes']) {
            $partyWins[$key] = ['party' => $partyName, 'votes' => $votes];
        }
    }

    // นับที่นั่งจากการชนะเขต
    $constituencyWins = [];
    foreach ($partyWins as $districtData) {
        $party = $districtData['party'];
        if (!isset($constituencyWins[$party])) {
            $constituencyWins[$party] = 0;
        }
        $constituencyWins[$party]++;
    }

    // สร้างข้อมูลพรรค
    $parties = [];
    $totalVotes = array_sum($partyVotes);

    // ใช้ข้อมูลที่นั่งจริง
    $yearSeatData = $seatData[$year] ?? [];

    foreach ($partyVotes as $partyName => $votes) {
        $cleanName = trim(preg_replace('/^พรรค/', '', $partyName));

        // หาข้อมูลที่นั่ง
        $seats = $yearSeatData[$cleanName] ?? null;
        if (!$seats) {
            // fallback to calculated constituency wins
            $constSeats = $constituencyWins[$partyName] ?? 0;
            $seats = ['constituency' => $constSeats, 'partyList' => 0];
        }

        $totalSeats = $seats['constituency'] + $seats['partyList'];

        // ข้ามพรรคที่ไม่มีที่นั่ง (แสดงเฉพาะพรรคที่ได้ที่นั่ง)
        if ($totalSeats <= 0) {
            continue;
        }

        $status = $partyStatus[$cleanName] ?? ['status' => 'active'];

        $parties[] = [
            'id' => getPartyId($partyName),
            'name' => 'พรรค'.$cleanName,
            'nameEn' => getPartyNameEn($partyName, $partyNamesEn),
            'color' => getPartyColor($partyName, $partyColors),
            'seats' => $totalSeats,
            'constituencySeats' => $seats['constituency'],
            'partyListSeats' => $seats['partyList'],
            'votes' => formatVotes($votes),
            'votesRaw' => $votes,
            'votePercentage' => number_format(($votes / $totalVotes) * 100, 1).'%',
            'seatPercentage' => 0, // จะคำนวณทีหลัง
            'leader' => $partyLeaders[$cleanName] ?? '',
            'policies' => $partyPolicies[$cleanName] ?? [],
            'status' => $status['status'],
            'dissolutionDate' => $status['dissolutionDate'] ?? null
        ];
    }

    // Sort by seats descending
    usort($parties, function ($a, $b) {
        return $b['seats'] - $a['seats'];
    });

    // Calculate seat percentage
    $totalSeats = array_sum(array_column($parties, 'seats'));
    foreach ($parties as &$party) {
        $party['seatPercentage'] = round(($party['seats'] / $totalSeats) * 100, 2);
    }
    unset($party);

    return $parties;
}

/**
 * Process 2554 JSON (party-level votes by province)
 */
function process2554Data($jsonFile, $seatData, $partyColors, $partyNamesEn, $partyLeaders, $partyPolicies)
{
    $json = json_decode(file_get_contents($jsonFile), true);
    $data = $json['data'];

    // หา header row ที่มีชื่อจังหวัด
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

    // รวมคะแนนแต่ละพรรค
    $partyVotes = [];

    foreach ($data as $row) {
        // ข้าม header
        if (!isset($row['A']) || !is_numeric($row['A'])) {
            continue;
        }

        $partyName = trim($row['B'] ?? '');
        if (empty($partyName) || mb_strpos($partyName, 'รวม') !== false) {
            continue;
        }

        // รวมคะแนนทุกจังหวัด
        $totalVotes = 0;
        foreach ($provinceColumns as $col => $province) {
            $votes = intval(str_replace(',', '', $row[$col] ?? 0));
            $totalVotes += $votes;
        }

        if ($totalVotes > 0) {
            $partyVotes[$partyName] = $totalVotes;
        }
    }

    // สร้างข้อมูลพรรค
    $parties = [];
    $grandTotal = array_sum($partyVotes);
    $yearSeatData = $seatData[2554] ?? [];

    foreach ($partyVotes as $partyName => $votes) {
        $cleanName = trim(preg_replace('/^พรรค/', '', $partyName));

        // หาข้อมูลที่นั่ง
        $seats = $yearSeatData[$cleanName] ?? null;
        if (!$seats) {
            $seats = ['constituency' => 0, 'partyList' => 0];
        }

        $totalSeats = $seats['constituency'] + $seats['partyList'];

        // ข้ามพรรคที่ไม่มีที่นั่ง
        if ($totalSeats <= 0) {
            continue;
        }

        $parties[] = [
            'id' => getPartyId($partyName),
            'name' => 'พรรค'.$cleanName,
            'nameEn' => getPartyNameEn($partyName, $partyNamesEn),
            'color' => getPartyColor($partyName, $partyColors),
            'seats' => $totalSeats,
            'constituencySeats' => $seats['constituency'],
            'partyListSeats' => $seats['partyList'],
            'votes' => formatVotes($votes),
            'votesRaw' => $votes,
            'votePercentage' => number_format(($votes / $grandTotal) * 100, 1).'%',
            'seatPercentage' => 0,
            'leader' => $partyLeaders[$cleanName] ?? '',
            'policies' => $partyPolicies[$cleanName] ?? [],
            'status' => 'active'
        ];
    }

    // Sort by seats descending
    usort($parties, function ($a, $b) {
        return $b['seats'] - $a['seats'];
    });

    // Calculate seat percentage
    $totalSeats = array_sum(array_column($parties, 'seats'));
    foreach ($parties as &$party) {
        $party['seatPercentage'] = round(($party['seats'] / $totalSeats) * 100, 2);
    }
    unset($party);

    return $parties;
}

/**
 * Format votes for display
 */
function formatVotes($votes)
{
    if ($votes >= 1000000) {
        return number_format($votes / 1000000, 1).' ล้าน';
    }
    return number_format($votes);
}

// Main execution
$dataDir = __DIR__.'/data';

echo "=== Processing Election Data ===\n\n";

// Process each year
$result = [];

// 2566
echo "Processing 2566...\n";
$parties2566 = processConstituencyData(
    $dataDir.'/2566.json',
    2566,
    $seatData,
    $partyColors,
    $partyNamesEn,
    $partyLeaders2566,
    $partyPolicies2566,
    $partyStatus
);
echo "  Found ".count($parties2566)." parties with seats\n";

$result[2566] = [
    'date' => $electionDates[2566],
    'national' => $nationalStats[2566],
    'parties' => $parties2566
];

// 2562
echo "Processing 2562...\n";
$parties2562 = processConstituencyData(
    $dataDir.'/2562.json',
    2562,
    $seatData,
    $partyColors,
    $partyNamesEn,
    $partyLeaders2562,
    $partyPolicies2562,
    $partyStatus
);
echo "  Found ".count($parties2562)." parties with seats\n";

$result[2562] = [
    'date' => $electionDates[2562],
    'national' => $nationalStats[2562],
    'parties' => $parties2562
];

// 2554
echo "Processing 2554...\n";
$parties2554 = process2554Data(
    $dataDir.'/2554.json',
    $seatData,
    $partyColors,
    $partyNamesEn,
    $partyLeaders2554,
    $partyPolicies2554
);
echo "  Found ".count($parties2554)." parties with seats\n";

$result[2554] = [
    'date' => $electionDates[2554],
    'national' => $nationalStats[2554],
    'parties' => $parties2554
];

// Generate election-data.js
$jsContent = "/**\n * Thai Election Data - Auto-generated\n * Generated: ".date('Y-m-d H:i:s')."\n */\n\n";
$jsContent .= "const electionDataFromXLS = ".json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).";\n";

$outputPath = $dataDir.'/election-data.js';
file_put_contents($outputPath, $jsContent);

echo "\n=== Complete ===\n";
echo "Generated: $outputPath\n";
echo "File size: ".number_format(filesize($outputPath))." bytes\n";

// Show summary
foreach ($result as $year => $data) {
    echo "\n$year:\n";
    echo "  Date: {$data['date']}\n";
    echo "  Parties: ".count($data['parties'])."\n";
    if (count($data['parties']) > 0) {
        echo "  Top 3:\n";
        foreach (array_slice($data['parties'], 0, 3) as $p) {
            echo "    - {$p['name']}: {$p['seats']} seats ({$p['votePercentage']})\n";
        }
    }
}
