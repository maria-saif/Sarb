<?php
session_start();
require_once __DIR__ . "/keywords_weights.php";
require_once __DIR__ . "/db_connect.php"; 

if (empty($_SESSION['transcribed_text'])) {
    die("<h2 style='color:red;text-align:center;'>No transcribed text found! Please run the analysis first.</h2>");
}

$transcript = $_SESSION['transcribed_text'];
$audio_path = $_SESSION['uploaded_audio_path'] ?? '';

$transcript_clean = trim(preg_replace('/[^\p{L}\p{N}\s]+/u', '', $transcript));
$transcript_clean = mb_strtolower($transcript_clean, 'UTF-8');

$weights = require __DIR__ . "/keywords_weights.php";
$risk_score = 0;
$found_keywords = [];

foreach ($weights as $keyword => $weight) {
    if (mb_stripos($transcript_clean, mb_strtolower($keyword,'UTF-8')) !== false) {
        $risk_score += $weight;
        $found_keywords[] = $keyword;
    }
}
$risk_score = min(100, $risk_score);

if ($risk_score >= 75) $risk_label = "🔴 High Risk";
elseif ($risk_score >= 40) $risk_label = "🟠 Medium Risk";
elseif ($risk_score >= 15) $risk_label = "🟡 Low Risk";
else $risk_label = "✅ Safe";

$sentences = preg_split('/(?<=[.!?؟])\s+/u', $transcript_clean, -1, PREG_SPLIT_NO_EMPTY);
$dangerous_sentences = [];
foreach ($sentences as $i => $s) {
    $matched = [];
    foreach ($found_keywords as $kw) {
        if (mb_stripos($s, mb_strtolower($kw,'UTF-8')) !== false) {
            $matched[] = $kw;
        }
    }
    if (!empty($matched)) {
        $dangerous_sentences[] = [
            "index" => $i+1,
            "text" => $s,
            "keywords" => $matched
        ];
    }
}

$filename = $_SESSION['uploaded_filename'] ?? 'Unknown File';
$user_id = $_SESSION['user_id'] ?? 0;
$keywords_json = json_encode($found_keywords);

$stmt = $conn->prepare("
    INSERT INTO analysis_history 
    (user_id, filename, transcript, suspicious_keywords, audio_path, result, risk_level, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("isssssi", $user_id, $filename, $transcript_clean, $keywords_json, $audio_path, $risk_label, $risk_score);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>سرب | Call Risk Assessment</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-[#1B263B] font-sans min-h-screen">

<header class="bg-white shadow sticky top-0 z-50 border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <img src="/Sarb/images/logo.svg" alt="Sarb Logo" class="w-12 h-12 drop-shadow-md">
      <a href="/Sarb/dashboard.php" class="text-2xl font-bold text-[#415A77]">سَرب</a>
    </div>
    <nav class="hidden md:flex items-center space-x-6 text-gray-700 font-medium">
      <a href="/Sarb/index.php" class="hover:text-[#415A77] transition">Dashboard</a>
      <a href="/Sarb/upload.php" class="hover:text-[#415A77] transition">Upload</a>
      <a href="/Sarb/my_analysis.php" class="hover:text-[#415A77] transition">My Analysis</a>
    </nav>
  </div>
</header>

<main class="max-w-5xl mx-auto px-6 py-16">

  <h1 class="text-3xl font-bold text-[#274C77] mb-6">Call Risk Assessment</h1>
  <p class="text-gray-600 mb-8">تحليل كامل للمكالمة اعتمادًا على النص، الصوت، ونمط السلوك الاحتيالي</p>

  <div class="bg-[#F0F4FF] p-6 rounded-xl shadow space-y-6">

    <div class="flex flex-col md:flex-row justify-between gap-4">
      <div class="flex-1 p-4 bg-[#E0ECFF] rounded-xl shadow">
        <p class="font-bold mb-2">Text Risk</p>
        <p class="text-2xl font-extrabold text-[#274C77]"><?= $risk_score ?>%</p>
        <p class="mt-1 text-sm text-gray-600 font-semibold"><?= $risk_label ?></p>
        <p class="mt-2 text-yellow-700 font-semibold">
          Suspicious Keywords: <?= !empty($found_keywords) ? implode(', ', $found_keywords) : 'None' ?>
        </p>
      </div>

      <div class="flex-1 p-4 bg-[#E0ECFF] rounded-xl shadow">
        <p class="font-bold mb-2">Voice Emotion</p>
        <p class="text-xl font-extrabold text-[#9B5DE5]">N/A</p>
        <p class="text-sm text-gray-600">• Not Available</p>
      </div>

      <div class="flex-1 p-4 bg-[#E0ECFF] rounded-xl shadow">
        <p class="font-bold mb-2">Robocall Probability</p>
        <p class="text-xl font-extrabold text-[#FF6D6D]">10%</p>
        <p class="text-sm text-gray-600">👤 Human-like Call</p>
      </div>
    </div>

    <?php if (!empty($dangerous_sentences)): ?>
    <div class="space-y-3">
      <h2 class="text-lg font-bold text-[#415A77]">Suspicious Sentences:</h2>
      <ul class="list-decimal ml-6 space-y-1">
      <?php foreach ($dangerous_sentences as $ds): ?>
        <li>
          <span class="font-semibold">Sentence <?= $ds['index'] ?>:</span> <?= $ds['text'] ?>
          <span class="text-sm text-yellow-700">(<?= implode(', ', $ds['keywords']) ?>)</span>
        </li>
      <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <div class="mt-6">
      <h2 class="text-lg font-bold text-[#415A77] mb-2">Transcript View:</h2>
      <div class="bg-white p-4 rounded-lg shadow border border-gray-200 text-gray-700">
        <?= htmlspecialchars($transcript_clean) ?>
      </div>
    </div>

    <?php if(!empty($audio_path) && file_exists($audio_path)): ?>
    <audio controls class="w-full mt-4 rounded-lg">
      <source src="<?= htmlspecialchars($audio_path) ?>" type="audio/mpeg">
    </audio>
    <?php endif; ?>

  </div>

</main>

<footer class="text-center text-gray-600 py-6 mt-12 border-t border-gray-200">
  © <?= date('Y') ?> سَرب — Secure voice, secure future
</footer>
</body>
</html>