<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "root"; 
$dbname = "sarb";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT filename, suspicious_keywords, risk_level, result, created_at FROM analysis_history ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سرب Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#E7ECEF] text-[#1B263B] font-sans min-h-screen">

  <header class="bg-white/95 backdrop-blur-md border-b border-gray-200 shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <img src="/Sarb/images/logo.svg" alt="Sarb Logo" class="w-24 h-24 drop-shadow-md">
        <a href="/Sarb/dashboard.php" class="text-2xl font-bold bg-gradient-to-r from-[#415A77] to-[#778DA9] text-transparent bg-clip-text tracking-wide">سَرب</a>
      </div>
      <nav class="hidden md:flex items-center space-x-6 text-gray-700 font-medium">
        <a href="/Sarb/index.php" class="hover:text-[#415A77] transition">Dashboard</a>
        <a href="/Sarb/upload.php" class="hover:text-[#415A77] transition">Upload</a>
        <a href="/Sarb/my_analysis.php" class="hover:text-[#415A77] transition">My Analysis</a>
      </nav>
      <button id="menu-btn" class="md:hidden text-gray-700 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
    <div id="mobile-menu" class="hidden md:hidden bg-white/95 border-t border-gray-200">
      <nav class="flex flex-col text-gray-700 font-medium px-6 py-4 space-y-3">
        <a href="/Sarb/dashboard.php" class="hover:text-[#415A77]">Dashboard</a>
        <a href="/Sarb/upload.php" class="hover:text-[#415A77]">Upload</a>
        <a href="/Sarb/my_analysis.php" class="hover:text-[#415A77]">My Analysis</a>
      </nav>
    </div>
  </header>

  <script>
    const menuBtn = document.getElementById("menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    menuBtn.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
    });
  </script>

  <section class="max-w-7xl mx-auto px-6 py-16 text-center bg-gradient-to-b from-[#E7ECEF] to-[#D6E0EB] rounded-b-3xl shadow-md">
    <h1 class="text-3xl md:text-4xl font-bold text-[#415A77] mb-4">Active Session</h1>
    <p class="text-gray-600 mb-8">Current Queue Overview</p>
    <div class="flex flex-col md:flex-row justify-center gap-4">
      <a href="/Sarb/upload.php" class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-[#415A77] to-[#778DA9] text-white shadow hover:scale-105 transition">↑ Upload Audio</a>
      <a href="/Sarb/my_analysis.php" class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-[#778DA9] to-[#415A77] text-white shadow hover:scale-105 transition">View My Analyses</a>
      <a href="/Sarb/live_listen.php" class="px-6 py-3 rounded-xl font-semibold bg-gradient-to-r from-[#415A77]/80 to-[#778DA9]/80 text-white shadow hover:scale-105 transition">🎧 Live AI Listening</a>
    </div>
  </section>

  <section class="max-w-7xl mx-auto px-6 py-12">
    <div class="overflow-x-auto shadow-lg rounded-xl">
      <table class="min-w-full text-left bg-white rounded-xl">
        <thead class="bg-[#415A77] text-white">
          <tr>
            <th class="px-6 py-3 font-semibold">Priority</th>
            <th class="px-6 py-3 font-semibold">File</th>
            <th class="px-6 py-3 font-semibold">Keywords</th>
            <th class="px-6 py-3 font-semibold">Risk</th>
            <th class="px-6 py-3 font-semibold">Time</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <?php
                $priority = $row['risk_level'] >= 70 ? 'High Priority' : ($row['risk_level'] >= 30 ? 'Medium Priority' : 'Low Priority');
                $keywords = is_array(json_decode($row['suspicious_keywords'])) ? implode(', ', json_decode($row['suspicious_keywords'])) : $row['suspicious_keywords'];
              ?>
              <tr>
                <td class="px-6 py-4"><?php echo $priority; ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['filename']); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($keywords); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['risk_level'] . '% (' . $row['result'] . ')'); ?></td>
                <td class="px-6 py-4"><?php echo htmlspecialchars($row['created_at']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">No active queue items. Upload audio or start Live AI Listening.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <footer class="text-center text-gray-600 py-6 mt-12 border-t border-gray-200">
    © 2026 سَرب — Secure voice, secure future
  </footer>

</body>
</html>

<?php $conn->close(); ?>