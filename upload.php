<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>سرب Upload</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#E7ECEF] text-[#1B263B] font-sans min-h-screen">

  <header class="bg-white/95 backdrop-blur-md border-b border-gray-200 shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

      <div class="flex items-center gap-3">
        <img src="/Sarb/images/logo.svg" alt="Sarb Logo" class="w-20 h-20 drop-shadow-md">
        <a href="/Sarb/dashboard.php" class="text-2xl font-bold bg-gradient-to-r from-[#415A77] to-[#778DA9] text-transparent bg-clip-text tracking-wide">
          سَرب
        </a>
      </div>

      <nav class="hidden md:flex items-center space-x-6 text-gray-700 font-medium">
        <a href="/Sarb/index.php" class="hover:text-[#415A77] transition">Dashboard</a>
        <a href="/Sarb/upload.php" class="hover:text-[#415A77] transition">Upload</a>
        <a href="/Sarb/my_analysis.php" class="hover:text-[#415A77] transition">My Analysis</a>
      </nav>

      <button id="menu-btn" class="md:hidden text-gray-300 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-[#274C77]">
      <nav class="flex flex-col text-gray-300 font-medium px-6 py-4 space-y-3">
        <a href="/Sarb/dashboard.php" class="hover:text-[#A3CEF1]">Dashboard</a>
        <a href="/Sarb/upload.php" class="hover:text-[#A3CEF1]">Upload</a>
        <a href="/Sarb/my_analysis.php" class="hover:text-[#A3CEF1]">My Analysis</a>
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

  <main class="flex items-start justify-center min-h-[80vh] px-4 py-16">
    <div class="bg-white rounded-2xl shadow-md p-10 max-w-md w-full text-center">

      <div class="relative mx-auto w-24 h-24 mb-6 flex items-center justify-center rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 shadow-[0_0_40px_-10px_rgba(124,58,237,0.7)]">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-white animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 10h1v4H4v-4zm3 0h1v4H7v-4zm3-2h1v8h-1V8zm3 1h1v6h-1V9zm3-2h1v10h-1V7zm3 3h1v4h-1v-4z"/>
        </svg>
        <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-indigo-400/50 to-purple-400/50 blur-xl opacity-70"></div>
      </div>

      <h2 class="text-2xl font-bold text-[#415A77] mb-2">Upload Audio File</h2>
      <p class="text-gray-600 mb-6">Upload your voice file for phishing analysis.</p>

      <form action="upload_process.php" method="POST" enctype="multipart/form-data" class="space-y-5" id="uploadForm">
        <label for="audioFile" class="border-2 border-dashed border-[#415A77] rounded-xl p-6 block cursor-pointer hover:bg-[#A3CEF1]/10">
          <p class="text-gray-700">🎤 Drag & Drop your file here or click to choose</p>
          <p class="text-xs text-gray-500 mt-1">Accepted: Any audio format</p>
          <input type="file" name="audioFile" id="audioFile" accept="audio/*" required class="hidden">
        </label>

        <button type="submit" class="w-full py-3 rounded-lg text-white font-semibold bg-gradient-to-r from-[#415A77] to-[#778DA9] shadow hover:scale-105 transition" id="analyzeBtn">
          🚀 Upload
        </button>
      </form>
    </div>
  </main>

  <footer class="text-center text-gray-600 py-6 mt-12 border-t border-gray-200">
    © 2026 سَرب — Secure voice, secure future
  </footer>

  <script>
    const dropZone = document.querySelector('#audioFile').parentElement;
    const input = document.getElementById('audioFile');
    const form = document.getElementById('uploadForm');
    const analyzeBtn = document.getElementById('analyzeBtn');

    dropZone.addEventListener('dragover', e => {
      e.preventDefault();
      dropZone.classList.add('bg-[#A3CEF1]/10');
    });

    dropZone.addEventListener('dragleave', e => {
      e.preventDefault();
      dropZone.classList.remove('bg-[#A3CEF1]/10');
    });

    dropZone.addEventListener('drop', e => {
      e.preventDefault();
      input.files = e.dataTransfer.files;
      dropZone.classList.remove('bg-[#A3CEF1]/10');
      if (input.files.length > 0) {
        analyzeBtn.disabled = true;
        analyzeBtn.innerText = '⏳ Processing...';
        form.submit();
      }
    });

    input.addEventListener('change', () => {
      if (input.files.length > 0) {
        analyzeBtn.disabled = true;
        analyzeBtn.innerText = '⏳ Processing...';
        form.submit();
      }
    });
  </script>

</body>
</html>