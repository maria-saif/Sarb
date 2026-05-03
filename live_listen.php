<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "root"; 
$dbname = "sarb"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🎧 Live AI Listening | سَرب</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#E7ECEF] text-[#1B263B] font-sans min-h-screen">

  <header class="bg-white/95 backdrop-blur-md border-b border-gray-200 shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

      <div class="flex items-center gap-3">
        <img src="/Sarb/images/logo.svg" alt="Sarb Logo" class="w-16 h-16 drop-shadow-md">
        <a href="/Sarb/index.php" class="text-2xl font-bold bg-gradient-to-r from-[#415A77] to-[#778DA9] text-transparent bg-clip-text tracking-wide">
          سَرب
        </a>
      </div>

      <nav class="hidden md:flex items-center space-x-6 text-gray-700 font-medium">
        <a href="/Sarb/index.php" class="hover:text-[#415A77] transition">Dashboard</a>
        <a href="/Sarb/upload.php" class="hover:text-[#415A77] transition">Upload</a>
        <a href="/Sarb/my_analysis.php" class="hover:text-[#415A77] transition">My Analysis</a>
      </nav>

      <button id="menu-btn" class="md:hidden text-white focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-[#415A77] border-t border-[#778DA9]">
      <nav class="flex flex-col text-white font-medium px-6 py-4 space-y-3">
        <a href="/Sarb/index.php" class="hover:text-[#A3CEF1]">Dashboard</a>
        <a href="/Sarb/upload.php" class="hover:text-[#A3CEF1]">Upload</a>
        <a href="/Sarb/my_analysis.php" class="hover:text-[#A3CEF1]">My Analysis</a>
      </nav>
    </div>
  </header>

  <script>
    const menuBtn = document.getElementById("menu-btn");
    const mobileMenu = document.getElementById("mobile-menu");
    menuBtn.addEventListener("click", () => { mobileMenu.classList.toggle("hidden"); });
  </script>

  <section class="max-w-4xl mx-auto px-6 py-16">
    <h1 class="text-3xl md:text-4xl font-bold text-[#415A77] mb-6 flex items-center gap-3">
      🎧 Live AI Call Analysis
      <span class="w-4 h-4 rounded-full bg-red-500 animate-pulse"></span>
    </h1>

    <div class="bg-white/80 backdrop-blur-md rounded-2xl p-8 shadow-md text-center">
      <button id="startButton" class="px-6 py-3 bg-[#415A77] hover:bg-[#778DA9] text-white font-semibold rounded-xl shadow-lg transition">
        🎤 Start Live Listening
      </button>
      <button id="stopButton" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-lg transition hidden mt-4">
        ⛔ Stop Live Listening
      </button>

      <div class="mt-6 text-sm text-gray-700">
        <label class="flex items-center justify-center gap-2">
          <input type="checkbox" id="consent" class="scale-110">
          I agree to recording/saving audio chunks for analysis
        </label>
        <p class="mt-2 text-xs text-gray-500 max-w-xl mx-auto">
          If unchecked, سَرب will analyze in real-time and discard the audio chunk immediately (no saving).
        </p>
      </div>

      <div class="mt-6 text-sm text-gray-600" id="codecInfo"></div>
      <div class="text-xs text-gray-600 mono" id="netInfo"></div>
      <div class="mt-3 text-xs text-emerald-500" id="saveStatus"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
      <div class="bg-white/80 backdrop-blur-md rounded-xl p-4 shadow-md">
        <div class="text-sm text-gray-600">Risk Level</div>
        <div id="riskLevel" class="text-3xl font-bold mt-2 text-red-500">— %</div>
      </div>
      <div class="bg-white/80 backdrop-blur-md rounded-xl p-4 shadow-md">
        <div class="text-sm text-gray-600">Suspicious Keywords</div>
        <div id="liveKeywords" class="text-lg mt-2 text-yellow-500">—</div>
      </div>
      <div class="bg-white/80 backdrop-blur-md rounded-xl p-4 shadow-md">
        <div class="text-sm text-gray-600">Detection Category</div>
        <div id="liveCategory" class="text-lg mt-2 text-blue-500">—</div>
      </div>
    </div>

    <div class="bg-white/80 backdrop-blur-md rounded-2xl p-4 mt-6 h-40 overflow-y-auto text-sm" id="liveTranscript">
      Waiting for audio…
    </div>

    <div class="bg-white/80 backdrop-blur-md rounded-2xl p-4 mt-6" id="liveAdvice">
      AI Safety Advice will appear here
    </div>

    <div class="mt-8 flex justify-between items-center">
      <h3 class="text-lg font-semibold">Incoming Alerts Queue</h3>
      <button id="clearQueue" class="text-xs px-3 py-1 bg-[#415A77] rounded-lg hover:bg-[#778DA9] text-white">Clear</button>
    </div>
    <div id="alertsQueue" class="space-y-2 text-sm text-gray-700 mt-2"></div>


  </section>

  <audio id="alertSound" src="alert.mp3" preload="auto"></audio>

  <script>
    let mediaRecorder, isRecording=false, activeStream=null, chunkTimer=null;

    document.getElementById("clearQueue").addEventListener("click",()=>{ document.getElementById("alertsQueue").innerHTML=""; });

    function pickMimeType(){ const types=["audio/webm;codecs=opus","audio/webm","audio/mp4","audio/aac"]; for(const t of types){ if(MediaRecorder.isTypeSupported(t)) return t; } return ""; }

    function apiUrl(file){ return new URL(file,window.location.href).toString(); }

    async function saveIncident(payload,modeLabel="Manual"){
      const statusEl=document.getElementById("saveStatus");
      statusEl.textContent="Saving incident...";
      const fd=new FormData();
      fd.append("text",payload.text||"");
      fd.append("risk",String(payload.risk||0));
      fd.append("category",payload.category||"Normal");
      fd.append("keywords",payload.keywords||"");
      try{
        const r=await fetch(apiUrl("live_save_incident.php"),{method:"POST",body:fd});
        const raw=await r.text();
        let d;
        try{ d=JSON.parse(raw); }catch(e){ d={ok:false,msg:"Non-JSON",raw}; }
        if(d.ok){ statusEl.textContent=`✅ Saved to Queue (${modeLabel}) • Incident ID: ${d.id}`; return {ok:true,id:d.id}; }
        else{ statusEl.textContent=`❌ Save failed (${modeLabel})`; return {ok:false}; }
      }catch(e){ statusEl.textContent="❌ Save failed (Fetch Error)"; return {ok:false}; }
    }

    document.getElementById("startButton").addEventListener("click",async()=>{
      if(isRecording) return;
      isRecording=true;
      const stream=await navigator.mediaDevices.getUserMedia({audio:true});
      activeStream=stream;
      const mimeType=pickMimeType();
      mediaRecorder=new MediaRecorder(stream,mimeType?{mimeType}:{});
      mediaRecorder.start();
      chunkTimer=setInterval(()=>{if(mediaRecorder.state==="recording") mediaRecorder.requestData();},3000);
      mediaRecorder.ondataavailable=(e)=>{ if(!isRecording) return; if(!e.data||e.data.size===0) return; sendChunk(e.data,mimeType); };
      mediaRecorder.onerror=(e)=>{ console.log("MediaRecorder error:",e); }
      document.getElementById("startButton").classList.add("hidden");
      document.getElementById("stopButton").classList.remove("hidden");
    });

    document.getElementById("stopButton").addEventListener("click",()=>{
      if(!isRecording) return;
      isRecording=false;
      if(chunkTimer){ clearInterval(chunkTimer); chunkTimer=null; }
      try{ mediaRecorder.stop(); }catch(e){}
      try{ if(activeStream) activeStream.getTracks().forEach(t=>t.stop()); }catch(e){}
      document.getElementById("stopButton").classList.add("hidden");
      document.getElementById("startButton").classList.remove("hidden");
      document.getElementById("liveTranscript").innerHTML += "<div class='text-red-400 mt-3'>[Listening Stopped]</div>";
    });

    async function sendChunk(blob,mimeType){
      const fd=new FormData();
      let filename="chunk.webm";
      if(mimeType?.includes("mp4")) filename="chunk.mp4";
      fd.append("audio_chunk",blob,filename);
      const consent=document.getElementById("consent").checked?"1":"0";
      fd.append("consent",consent);
      try{
        const r=await fetch(apiUrl("live_process.php"),{method:"POST",body:fd});
        const raw=await r.text();
        let d;
        try{ d=JSON.parse(raw);}catch(e){console.log("Non-JSON",raw); return;}
        updateUI(d);
      }catch(e){console.log("Fetch Error",e);}
    }

    function updateUI(data){
      const risk=data.risk||0;
      document.getElementById("riskLevel").innerText=risk+"%";
      document.getElementById("liveKeywords").innerText=data.keywords||"—";
      document.getElementById("liveCategory").innerText=data.category||"—";
      if(risk>=75){ document.getElementById("alertSound").play().catch(()=>{}); }
      if(data.text&&data.text.trim()!==""){
        const box=document.getElementById("liveTranscript");
        if(box.innerText.includes("Waiting")) box.innerHTML="";
        box.innerHTML+="<div>"+escapeHtml(data.text)+"</div>";
        box.scrollTop=box.scrollHeight;
      }
    }

    function escapeHtml(str){return String(str).replaceAll("&","&amp;").replaceAll("<","&lt;").replaceAll(">","&gt;").replaceAll('"',"&quot;").replaceAll("'","&#039;");}
  </script>

</body>
</html>