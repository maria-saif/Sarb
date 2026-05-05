<?php
session_start();
require_once "includes/db_connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT a.*, u.name AS user_name, u.email 
                        FROM analysis_history a 
                        JOIN users u ON a.user_id = u.id 
                        WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<h2 style='text-align:center;color:red;'>Analysis not found.</h2>");
}

$row = $result->fetch_assoc();

$keywords = json_decode($row['suspicious_keywords'], true);
if (!is_array($keywords)) $keywords = [];

$risk = (float)$row['risk_level'];
if ($risk >= 70) {
    $risk_color = 'bg-red-600/80 border-red-400';
    $risk_label = '🔴 High Risk';
} elseif ($risk >= 40) {
    $risk_color = 'bg-yellow-500/80 border-yellow-300';
    $risk_label = '🟠 Suspicious';
} elseif ($risk >= 20) {
    $risk_color = 'bg-yellow-300/80 border-yellow-200';
    $risk_label = '🟡 Low Risk';
} else {
    $risk_color = 'bg-green-600/80 border-green-400';
    $risk_label = '✅ Safe';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Analysis | سَرب</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(160deg,#0b1124,#1a1635,#2a1b59);
  color:#e5e7eb;
  min-height:100vh;
}
.card{
  background:rgba(255,255,255,0.05);
  border:1px solid rgba(255,255,255,0.08);
  backdrop-filter:blur(14px);
  border-radius:1rem;
  padding:2rem;
}
.glow-btn{
  background:linear-gradient(90deg,#2563eb,#7c3aed);
  box-shadow:0 0 25px rgba(124,58,237,0.4);
  transition:.3s;
}
.glow-btn:hover{transform:scale(1.05);}
</style>
</head>
<body>
<?php include "includes/header.php"; ?>

<main class="flex justify-center items-start pt-28 pb-16 px-4">
<div class="card max-w-3xl w-full">
  <h1 class="text-3xl font-bold mb-6 bg-gradient-to-r from-indigo-400 to-purple-500 bg-clip-text text-transparent">
    🧠 Analysis Details
  </h1>

  <h3 class="text-lg mb-2 text-gray-300">👤 User:</h3>
  <p class="mb-4 text-sm text-gray-400"><?= htmlspecialchars($row['user_name']) ?> — <?= htmlspecialchars($row['email']) ?></p>

  <h3 class="text-lg mb-2 text-gray-300">🎙️ Transcribed Text:</h3>
  <div class="bg-gray-800/50 p-4 rounded-xl border border-gray-700 text-sm text-gray-300 whitespace-pre-wrap mb-6">
    <?= nl2br(htmlspecialchars($row['transcript'] ?? 'No transcript available')) ?>
  </div>

  <?php if (!empty($row['audio_path']) && file_exists($row['audio_path'])): ?>
    <h3 class="text-lg mb-2 text-gray-300">🎧 Original Audio:</h3>
    <audio controls class="w-full rounded-lg mb-6">
      <source src="<?= htmlspecialchars($row['audio_path']) ?>" type="audio/mpeg">
      Your browser does not support the audio element.
    </audio>
  <?php endif; ?>

  <h3 class="text-lg mb-2 text-gray-300">⚠️ Risk Level:</h3>
  <div class="w-full bg-gray-700/50 rounded-full h-6 mb-4 overflow-hidden">
    <div class="h-6 flex items-center justify-center text-white text-xs font-semibold"
         style="width: <?= min($risk,100) ?>%; background:linear-gradient(90deg,#7c3aed,#2563eb);">
      <?= $risk ?>%
    </div>
  </div>
  <div class="p-4 rounded-xl font-bold text-lg text-white border <?= $risk_color ?> mb-6">
    <?= $risk_label ?>
  </div>

  <?php if (!empty($keywords)): ?>
  <h3 class="text-lg mb-2 text-gray-300">🔍 Suspicious Keywords:</h3>
  <div class="bg-yellow-100/10 text-yellow-300 p-3 rounded-xl border border-yellow-500/40 mb-6">
    <?= htmlspecialchars(implode(', ', $keywords)) ?>
  </div>
  <?php endif; ?>

  <div class="flex justify-center gap-4">
    <a href="all_analyses.php" class="glow-btn px-6 py-2 rounded-lg text-white font-semibold">← Back</a>
    <a href="generate_pdf.php?id=<?= (int)$row['id'] ?>" target="_blank" class="glow-btn px-6 py-2 rounded-lg text-white font-semibold">📄 PDF Report</a>
  </div>
</div>
</main>

<footer class="text-center text-gray-500 text-sm py-6 border-t border-white/10 mt-10">
 © <?= date('Y') ?> سَرب — Call Center
</footer>
</body>
</html>