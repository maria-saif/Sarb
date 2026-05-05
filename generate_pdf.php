<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();
use Mpdf\Mpdf;

$text       = $_SESSION['transcribed_text'] ?? 'لا يوجد نص';
$transcript = $_SESSION['transcribed_text'] ?? '';
$weights    = $_SESSION['weights'] ?? []; 

require_once __DIR__ . "/risk_engine.php";
$analysis = vs_analyze_text($transcript, $weights);

$risk_level     = $analysis["risk"];            
$risk_label     = $analysis["category"];        
$voice_emotion  = $analysis["voice_emotion"] ?? 'N/A';
$robocall_prob  = $analysis["robocall_prob"] ?? 0;
$found_keywords = $analysis["keywords"];       

$_SESSION['risk_level']     = $risk_level;
$_SESSION['risk_label']     = $risk_label;
$_SESSION['voice_emotion']  = $voice_emotion;
$_SESSION['robocall_prob']  = $robocall_prob;
$_SESSION['found_keywords'] = $found_keywords;

$filename = $_SESSION['uploaded_filename'] ?? 'Unknown File';
$username = $_SESSION['username'] ?? 'Guest';
$date     = date("Y-m-d H:i:s");

$is_arabic = preg_match('/\p{Arabic}/u', $text);

$keywords_text = !empty($found_keywords) ? implode(', ', $found_keywords) : ($is_arabic ? 'لا توجد كلمات مشبوهة' : 'None');

$logo_path = __DIR__ . "/images/logo.svg";
$logo_html = file_exists($logo_path) ? "<img src='$logo_path' width='100'>" : "";

$html = "
<html lang='".($is_arabic?'ar':'en')."' dir='".($is_arabic?'rtl':'ltr')."'>
<head>
<meta charset='UTF-8'>
<style>
body { font-family:'Cairo','Poppins',sans-serif; background:#f7f9fc; color:#1B263B; padding:20px; }
.header { text-align:center; margin-bottom:20px; }
.header img { margin-bottom:10px; }
.header h1 { font-size:18pt; font-weight:700; color:#274C77; }
.card { background:#e6f0ff; border-radius:10px; border:1px solid #d1d9e6; padding:15px; margin-bottom:10px; }
.label { font-weight:600; color:#274C77; }
.value { color:#1B263B; }
.section-title { font-weight:700; color:#274C77; margin-top:10px; margin-bottom:5px; }
.stat-card { background:#d9e5ff; border-radius:8px; padding:10px; display:inline-block; width:32%; margin-right:1%; vertical-align:top; text-align:center; }
.stat-card span { display:block; font-weight:700; margin-top:5px; }
</style>
</head>
<body>
<div class='header'>
    $logo_html
    <h1>".($is_arabic?'تقرير تحليل الاحتيال الصوتي':'Sarb Voice Analysis Report')."</h1>
</div>

<div class='card'>
    <div><span class='label'>".($is_arabic?'المستخدم':'User').":</span> <span class='value'>$username</span></div>
    <div><span class='label'>".($is_arabic?'اسم الملف':'Filename').":</span> <span class='value'>$filename</span></div>
    <div><span class='label'>".($is_arabic?'التاريخ':'Date').":</span> <span class='value'>$date</span></div>
    <div><span class='label'>".($is_arabic?'مستوى الخطورة':'Risk Level').":</span> <span class='value'>$risk_level%</span></div>
    <div><span class='label'>".($is_arabic?'النتيجة':'Result').":</span> <span class='value'>$risk_label</span></div>
</div>

<div class='card'>
    <div class='section-title'>".($is_arabic?'الكلمات المشبوهة':'Suspicious Keywords').":</div>
    <div class='value'>$keywords_text</div>
</div>

<div class='card'>
    <div class='section-title'>Text Risk, Voice Emotion & Robocall Probability:</div>
    <div class='stat-card'>
        <span>".($is_arabic?'Text Risk':'Text Risk')."</span>
        <span>$risk_level%</span>
    </div>
    <div class='stat-card'>
        <span>".($is_arabic?'Voice Emotion':'Voice Emotion')."</span>
        <span>$voice_emotion</span>
    </div>
    <div class='stat-card'>
        <span>".($is_arabic?'Robocall Probability':'Robocall Probability')."</span>
        <span>$robocall_prob%</span>
    </div>
</div>

<div class='card'>
    <div class='section-title'>".($is_arabic?'النص المحوّل':'Transcribed Text').":</div>
    <div class='value'>".nl2br(htmlspecialchars($text))."</div>
</div>

</body>
</html>
";

$mpdf = new Mpdf(['mode'=>'utf-8','format'=>'A4', 'default_font' => 'Cairo']);
$mpdf->WriteHTML($html);
$mpdf->Output('Sarb_Analysis_Report.pdf','D');
exit;
?>