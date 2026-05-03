<?php
session_start();
require_once "includes/db_connection.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
 
}

$meta = $conn->query("
  SELECT
    (SELECT COUNT(*) FROM users) AS users_count,
    (SELECT COUNT(*) FROM analysis_history) AS analysis_count,
    (SELECT MAX(created_at) FROM analysis_history) AS last_analysis
")->fetch_assoc() ?? ['users_count'=>0,'analysis_count'=>0,'last_analysis'=>null];

$users_count       = (int)($meta['users_count'] ?? 0);
$analysis_count    = (int)($meta['analysis_count'] ?? 0);
$last_analysis_raw = $meta['last_analysis'] ?? null;

$br = $conn->query("
  SELECT
    SUM(CASE WHEN result='Safe' THEN 1 ELSE 0 END)        AS c_safe,
    SUM(CASE WHEN result='Suspicious' THEN 1 ELSE 0 END)  AS c_suspicious
  FROM analysis_history
")->fetch_assoc() ?? ['c_safe'=>0,'c_suspicious'=>0];

$count_safe       = (int)($br['c_safe'] ?? 0);
$count_suspicious = (int)($br['c_suspicious'] ?? 0);

$total        = $count_safe + $count_suspicious;
$risk_percent = $total > 0 ? round(($count_suspicious / $total) * 100, 1) : 0;
$last_analysis = $last_analysis_raw ? date('Y-m-d H:i', strtotime($last_analysis_raw)) : 'No data';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Statistics | سَرب</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-950 via-indigo-950 to-purple-950 text-slate-200 font-[Poppins]">

<?php include "includes/header.php"; ?>

<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-3xl md:text-4xl font-bold mb-8 text-center">
    📊 سَرب System Statistics
  </h1>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-5 shadow-[0_0_40px_-15px_rgba(99,102,241,0.5)] hover:shadow-[0_0_55px_-10px_rgba(99,102,241,0.7)] transition">
      <h2 class="text-sm uppercase tracking-wide text-slate-300">👥 Users</h2>
      <p class="text-3xl font-extrabold mt-2 text-white"><?= number_format($users_count) ?></p>
    </div>
    <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-5 shadow-[0_0_40px_-15px_rgba(168,85,247,0.5)] hover:shadow-[0_0_55px_-10px_rgba(168,85,247,0.7)] transition">
      <h2 class="text-sm uppercase tracking-wide text-slate-300">📂 Total Analyses</h2>
      <p class="text-3xl font-extrabold mt-2 text-white"><?= number_format($analysis_count) ?></p>
    </div>
    <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-5 shadow-[0_0_40px_-15px_rgba(34,197,94,0.4)] hover:shadow-[0_0_55px_-10px_rgba(34,197,94,0.6)] transition">
      <h2 class="text-sm uppercase tracking-wide text-slate-300">🕒 Last Analysis</h2>
      <p class="text-base mt-2 text-slate-200"><?= htmlspecialchars($last_analysis) ?></p>
    </div>
  </div>

  <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-6 md:p-8 shadow-xl">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl md:text-2xl font-bold">📉 Analysis Breakdown</h2>
      <span class="text-sm text-slate-300">
        Phishing Risk: <span class="font-semibold text-yellow-300"><?= $risk_percent ?>%</span>
      </span>
    </div>

    <div class="grid md:grid-cols-2 gap-8 items-center">
      <div class="flex justify-center">
        <div class="p-4 rounded-full bg-white/5 backdrop-blur shadow-[0_0_40px_-10px_rgba(168,85,247,0.5)]">
          <canvas id="analysisChart" class="max-w-[280px]"></canvas>
        </div>
      </div>

      <div class="text-sm md:text-base space-y-3">
        <p class="flex items-center gap-3">
          <span class="inline-block w-3 h-3 rounded-full" style="background:#34d399"></span>
          <span class="text-slate-300">Safe:</span>
          <span class="font-semibold text-white"><?= number_format($count_safe) ?></span>
        </p>
        <p class="flex items-center gap-3">
          <span class="inline-block w-3 h-3 rounded-full" style="background:#f87171"></span>
          <span class="text-slate-300">Suspicious:</span>
          <span class="font-semibold text-white"><?= number_format($count_suspicious) ?></span>
        </p>

        <div class="pt-4">
          <div class="text-slate-300 mb-2">Overall Risk</div>
          <div class="w-full h-3 bg-white/10 rounded-full overflow-hidden">
            <div class="h-full rounded-full"
                 style="width: <?= $risk_percent ?>%; background: linear-gradient(90deg,#fbbf24,#f59e0b);"></div>
          </div>
          <div class="mt-2 text-slate-400 text-sm">Based on Suspicious / (Safe + Suspicious)</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php @include "includes/footer.php"; ?>

<script>
const ctx = document.getElementById('analysisChart').getContext('2d');
new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ['Safe', 'Suspicious'],
    datasets: [{
      data: [<?= $count_safe ?>, <?= $count_suspicious ?>],
      backgroundColor: ['#34d399', '#f87171'],
      borderColor: 'rgba(255,255,255,0.08)',
      borderWidth: 2,
      hoverOffset: 8
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { color: '#cbd5e1' }
      },
      tooltip: {
        bodyColor: '#e2e8f0',
        titleColor: '#e2e8f0',
        backgroundColor: 'rgba(2,6,23,0.9)',
        borderColor: 'rgba(255,255,255,0.06)',
        borderWidth: 1
      }
    }
  }
});
</script>

</body>
</html>