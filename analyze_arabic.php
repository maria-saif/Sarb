<?php
echo "🟢 analyze_arabic.php loaded<br>";
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$audio_path = "uploads/arabic_scam_audio.wav";
$command = escapeshellcmd("python3 arabic_transcribe.py " . escapeshellarg($audio_path));
$output = shell_exec($command);

if (!$output) {
    echo "<h2 style='color:red'>❌ Python script did not return any output.</h2>";
    exit;
}

$result = json_decode($output, true);

if (!$result || !isset($result['transcript'])) {
    echo "<h2 style='color:red'>❌ Failed to decode Python output.</h2>";
    exit;
}

$text = $result['transcript'];
$found = $result['found_keywords'];
$risk_level = $result['risk_level'];
$risk_percent = $result['risk_percent'];
$isSuspicious = $risk_percent >= 40;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Call Analysis | سَرب</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 p-8">
  <div class="max-w-3xl mx-auto bg-white shadow-md rounded-xl p-6">

    <h1 class="text-2xl font-bold mb-4">🎙️ Transcribed Text</h1>
    <div class="bg-gray-100 p-4 rounded border border-gray-300 whitespace-pre-wrap"><?= htmlspecialchars($text) ?></div>

    <h2 class="text-xl font-semibold mt-6 mb-2">⚠️ Risk Level:</h2>
    <div class="w-full bg-gray-200 rounded-full h-6 mb-4">
      <div class="h-6 rounded-full text-white text-center leading-6 transition-all duration-700"
           style="width: <?= $risk_percent ?>%; background-color: <?= 
             $risk_percent >= 70 ? '#dc2626' : 
             ($risk_percent >= 40 ? '#facc15' : '#16a34a') ?>;">
        <?= $risk_percent ?>%
      </div>
    </div>

    <div class="text-white p-4 rounded text-lg font-bold <?= $isSuspicious ? 'bg-red-600' : 'bg-green-600' ?>">
      <?= $isSuspicious ? '⚠️ Suspicious content detected' : '✅ The call is safe' ?>
    </div>

    <div class="mt-6">
      <h3 class="font-bold mb-2">🔎 Detected Suspicious Keywords:</h3>
      <?php if (count($found) > 0): ?>
        <div class="bg-yellow-100 p-3 rounded border border-yellow-400 text-gray-800">
          <?= implode(", ", array_unique($found)) ?>
        </div>
      <?php else: ?>
        <p>No suspicious keywords found.</p>
      <?php endif; ?>
    </div>

    <div class="mt-6 text-center">
      <a href="dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow transition">
        ← Back to Dashboard
      </a>
    </div>
  </div>
</body>
</html>