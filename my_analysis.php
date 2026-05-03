<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/db_connect.php"; 

$stmt = $conn->prepare("
    SELECT u.id AS upload_id, u.filename,
           a.transcript, a.suspicious_keywords, a.risk_level, a.audio_path
    FROM uploads u
    LEFT JOIN analysis_history a ON u.id = a.upload_id
    ORDER BY u.id DESC
");
$stmt->execute();
$result = $stmt->get_result();
$analyses = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>سرب | My Analysis</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#E7ECEF] text-[#1B263B] font-sans min-h-screen">

<header class="bg-white/95 backdrop-blur-md border-b border-gray-200 shadow sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <div class="flex items-center gap-3">
      <img src="/Sarb/images/logo.svg" alt="Sarb Logo" class="w-9 h-9 drop-shadow-md">
      <a href="/Sarb/dashboard.php" class="text-2xl font-bold bg-gradient-to-r from-[#415A77] to-[#778DA9] text-transparent bg-clip-text tracking-wide">
        سَرب
      </a>
    </div>
    <nav class="hidden md:flex items-center space-x-6 text-gray-700 font-medium">
      <a href="/Sarb/index.php" class="hover:text-[#415A77] transition">Dashboard</a>
      <a href="/Sarb/upload.php" class="hover:text-[#415A77] transition">Upload</a>
      <a href="/Sarb/my_analysis.php" class="hover:text-[#415A77] transition">My Analysis</a>
    </nav>
  </div>
</header>

<main class="max-w-7xl mx-auto px-6 py-16">
  <h1 class="text-3xl md:text-4xl font-bold text-[#415A77] mb-8 text-center">My Analysis</h1>

  <?php if (empty($analyses)): ?>
    <p class="text-gray-600 text-center">No analysis items available.</p>
  <?php else: ?>
    <div class="grid md:grid-cols-2 gap-6">
      <?php foreach ($analyses as $a): ?>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
          <h2 class="font-bold text-lg mb-2"><?= htmlspecialchars($a['filename']) ?></h2>
          <p class="text-gray-700 mb-2">Risk Level: <?= $a['risk_level'] ?? 'N/A' ?>%</p>
          <?php if (!empty($a['suspicious_keywords'])): ?>
            <p class="text-yellow-700 text-sm">Keywords: <?= htmlspecialchars(implode(', ', json_decode($a['suspicious_keywords'], true))) ?></p>
          <?php endif; ?>
          <?php if (!empty($a['transcript'])): ?>
            <p class="text-gray-600 text-sm mt-2 truncate"><?= htmlspecialchars($a['transcript']) ?></p>
          <?php endif; ?>
          <?php if (!empty($a['audio_path'])): ?>
            <audio controls class="mt-3 w-full rounded-lg">
              <source src="/Sarb/<?= htmlspecialchars($a['audio_path']) ?>" type="audio/m4a">
              Your browser does not support the audio element.
            </audio>
          <?php endif; ?>
          <a href="/Sarb/result.php?upload_id=<?= $a['upload_id'] ?>" class="mt-4 inline-block px-4 py-2 bg-gradient-to-r from-[#415A77] to-[#778DA9] text-white rounded-lg hover:scale-105 transition">View Result</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<footer class="text-center text-gray-600 py-6 mt-12 border-t border-gray-200">
  © <?= date('Y') ?> سَرب — Secure voice, secure future
</footer>

</body>
</html>