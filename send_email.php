<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========== قراءة البيانات القادمة من result.php ==========
$filename        = $_POST['filename']        ?? 'audio_file';
$text_risk       = $_POST['text_risk']       ?? 0;
$text_risk_label = $_POST['text_risk_label'] ?? 'N/A';

// --- keywords قد تصل كـ string وليس array
$found_keywords  = $_POST['found_keywords'] ?? [];
if (!is_array($found_keywords)) {
    $found_keywords = array_filter(array_map('trim', explode(',', $found_keywords)));
}

// --- dangerous_sentences قد تصل كـ نص وليس array
$dangerous_sentences = $_POST['dangerous_sentences'] ?? [];
if (!is_array($dangerous_sentences)) {
    $lines = explode("\n", $dangerous_sentences);
    $dangerous_sentences = [];
    foreach ($lines as $line) {
        $dangerous_sentences[] = [
            "index" => "",
            "text" => $line,
            "keywords" => []
        ];
    }
}

$overall_risk    = $_POST['overall_risk']    ?? 0;
$overall_label   = $_POST['overall_label']   ?? 'N/A';
$robocall_score  = $_POST['robocall_score']  ?? 0;
$robocall_label  = $_POST['robocall_label']  ?? 'N/A';
$emotion_score   = $_POST['emotion_score']   ?? 'N/A';
$emotion_label   = $_POST['emotion_label']   ?? 'N/A';
$transcript      = $_POST['transcript']      ?? 'No transcript.';
$audio_url       = $_POST['audio_url']       ?? '';

$username        = $_SESSION['username']     ?? 'User';
$user_email      = $_SESSION['email']        ?? '';

if (!$user_email) return;

// ========== إنشاء HTML للرسالة ==========
$keywords_html = '';
foreach ($found_keywords as $kw) {
    $kw = htmlspecialchars($kw, ENT_QUOTES, 'UTF-8');
    $keywords_html .= "<span style='background:#fff1a8;padding:4px 8px;border-radius:6px;margin:3px;
                       display:inline-block;font-size:13px;color:#553900'>{$kw}</span>";
}

$sentences_html = '';
foreach ($dangerous_sentences as $item) {
    $idx = $item['index'] ?? '';
    $sentence = htmlspecialchars($item['text'] ?? '', ENT_QUOTES, 'UTF-8');
    $kw_list  = '';
    if (isset($item['keywords']) && is_array($item['keywords'])) {
        $kw_list = implode(', ', $item['keywords']);
    }
    $sentences_html .= "
        <tr>
            <td style='padding:8px;border-bottom:1px solid #ddd;'>{$idx}</td>
            <td style='padding:8px;border-bottom:1px solid #ddd;'>{$sentence}</td>
            <td style='padding:8px;border-bottom:1px solid #ddd;color:#c77d00;'>{$kw_list}</td>
        </tr>
    ";
}

$audio_section = $audio_url
    ? "<a href='{$audio_url}' style='color:#2563eb;font-weight:bold;'>🎧 Listen to Audio File</a>"
    : "—";

$body = "
<html>
<body style='font-family:Arial;background:#f5f6fa;padding:20px;color:#333;'>

<div style='background:#fff;border-radius:10px;padding:25px;max-width:700px;margin:auto;border:1px solid #ddd;'>

<h2 style='text-align:center;color:#4b0082;'>🔔 سَرب — Analysis Report</h2>

<h3>👤 User: {$username}</h3>
<p><strong>File:</strong> {$filename}</p>

<hr style='margin:20px 0;'>

<h3>📊 Overall Risk</h3>
<p style='font-size:22px;color:#4b0082;font-weight:bold;'>{$overall_risk}% — {$overall_label}</p>

<hr style='margin:20px 0;'>

<h3>📝 Text Analysis</h3>
<ul>
  <li><strong>Text Risk:</strong> {$text_risk}%</li>
  <li><strong>Label:</strong> {$text_risk_label}</li>
  <li><strong>Keywords:</strong><br>{$keywords_html}</li>
</ul>

<h3>🎤 Emotion Analysis</h3>
<ul>
  <li><strong>Emotion Score:</strong> {$emotion_score}</li>
  <li><strong>Status:</strong> {$emotion_label}</li>
</ul>

<h3>🤖 Robocall Detection</h3>
<ul>
  <li><strong>Probability:</strong> {$robocall_score}%</li>
  <li><strong>Status:</strong> {$robocall_label}</li>
</ul>

<hr style='margin:20px 0;'>

<h3>🔎 Suspicious Sentences</h3>
<table style='width:100%;border-collapse:collapse;font-size:14px;'>
    <thead>
        <tr style='background:#eee;font-weight:bold;'>
            <th style='padding:8px;border-bottom:1px solid #ccc;'>#</th>
            <th style='padding:8px;border-bottom:1px solid #ccc;'>Sentence</th>
            <th style='padding:8px;border-bottom:1px solid #ccc;'>Keywords</th>
        </tr>
    </thead>
    <tbody>
        {$sentences_html}
    </tbody>
</table>

<hr style='margin:20px 0;'>

<h3>🗣 Transcript</h3>
<p style='background:#f8f8f8;padding:15px;border-radius:8px;border:1px solid #ddd;font-size:14px;line-height:1.6;'>{$transcript}</p>

<hr style='margin:20px 0;'>

<h3>🎧 Audio File</h3>
<p>{$audio_section}</p>

<hr style='margin:20px 0;'>

<p style='text-align:center;color:#999;font-size:13px;'>© سَرب — Automated Phishing Analysis</p>

</div>

</body>
</html>
";

// ========== إرسال الإيميل ==========
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'mariasaif3072005@gmail.com';
    $mail->Password   = 'zcin rbnx pgrd girg';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);

    $mail->setFrom('mariasaif3072005@gmail.com', 'سَرب');
    $mail->addAddress($user_email);

    $mail->Subject = "🔔 سَرب Analysis Result";
    $mail->Body    = $body;

    $mail->send();
} catch (Exception $e) {
    error_log("Email error: {$mail->ErrorInfo}");
}
?>