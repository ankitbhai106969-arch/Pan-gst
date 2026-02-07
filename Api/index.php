<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// ===== INPUT CHECK =====
if (!isset($_GET['pan']) || empty($_GET['pan'])) {
    echo json_encode([
        "status" => false,
        "message" => "pan parameter missing",
        "developer" => "@xhamsterpaglu"
    ]);
    exit;
}

$pan = strtoupper(trim($_GET['pan']));

// basic PAN validation
if (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan)) {
    echo json_encode([
        "status" => false,
        "message" => "invalid PAN format",
        "developer" => "@xhamsterpaglu"
    ]);
    exit;
}

// ===== SOURCE API =====
$url = "https://razorpay.com/api/gstin/pan/" . $pan;

// ===== CURL REQUEST =====
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "User-Agent: Mozilla/5.0",
        "Accept: application/json",
        "Referer: https://razorpay.com/gst-number-search/pan/"
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode([
        "status" => false,
        "message" => "Source API error",
        "developer" => "@xhameterpaglu"
    ]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// ===== JSON DECODE =====
$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        "status" => false,
        "message" => "No data found",
        "developer" => "@xhamsterpaglu"
    ]);
    exit;
}

// ===== FINAL OUTPUT =====
$final = [
    "status" => true,
    "pan" => $pan,
    "data" => $data,
    "developer" => "@xhamsterpaglu"
];

echo json_encode($final, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
