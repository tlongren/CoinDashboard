<?php

header('Content-type: application/json');

$failureOutput = json_encode((object)['status' => 'failed'], JSON_PRETTY_PRINT);

$coins = [
    // [Currency, [Investment, Balance]]
    ['ETH', [25, 0.50]],
    ['ETH', [30, 0.40]],
    ['NLG', [150, 4000]],
    ['NLG', [200, 2500]]
];

$apiUrl = 'https://api.coinmarketcap.com/v1/ticker/?convert=EUR';

$data = file_get_contents($apiUrl);

if (!$data) {
    die($failureOutput);
}

$parsedData = json_decode($data);

if (json_last_error() !== JSON_ERROR_NONE) {
    die($failureOutput);
}

$parsedData = array_filter($parsedData, function($coin) use ($coins): bool {
    return in_array($coin->symbol, array_column($coins, 0));
});

$parsedData = array_combine(array_column($parsedData, 'symbol'), $parsedData);

$returnData = [];

foreach ($coins as list($symbol, list($investment, $balance))) {
    $returnData[] = [
        'investment' => $investment,
        'symbol' => $symbol,
        'value' => $balance * floatval($parsedData[$symbol]->price_eur),
        'change' => [
            '1hr' => $parsedData[$symbol]->percent_change_1h,
            '24hr' => $parsedData[$symbol]->percent_change_24h,
            '7d' => $parsedData[$symbol]->percent_change_7d
        ]
    ];
}

echo json_encode($returnData, JSON_PRETTY_PRINT);
