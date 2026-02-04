/**
 * InclusiveQuest Watch Page
 * - Supports Mux HLS via playbackId -> https://stream.mux.com/{id}.m3u8
 * - Supports YouTube embeds via IFrame API
 * - Renders ASL avatar panel below/side; basic sync to main playback
 */
(function () {
  const shell = document.querySelector(".iq-watch__playerShell");
  if (!shell) return;

  const sourceType = shell.dataset.iqSource || "mux";
  const muxPlaybackId = shell.dataset.iqMux || "";
  const youtubeUrl = shell.dataset.iqYoutube || "";
  const aslAssetUrl = shell.dataset.iqAsl || "";
  const aslDefault = shell.dataset.iqAslDefault || "below";

  const aslVideo = document.getElementById("iq-asl-video");
  const mainVideo = document.getElementById("iq-main-video");
  const ytMount = document.getElementById("iq-yt");

  const btnToggle = document.querySelector('[data-iq-action="asl-toggle"]');
  const selPosition = document.querySelector('[data-iq-action="asl-position"]');
  const selSize = document.querySelector('[data-iq-action="asl-size"]');

  let aslEnabled = true;
  let ytPlayer = null;
  let ytSyncTimer = null;

  // Init UI defaults
  if (selPosition) selPosition.value = aslDefault;
  applyPosition(aslDefault);

  if (aslVideo && aslAssetUrl) {
    aslVideo.src = aslAssetUrl;
    aslVideo.preload = "auto";
    aslVideo.loop = false; // usually you want to match the main playback, not loop
  }

  // Size control (simple)
  if (selSize) {
    selSize.addEventListener("change", (e) => {
      const v = e.target.value;
      shell.classList.remove("iq-aslSize-sm", "iq-aslSize-md", "iq-aslSize-lg");
      shell.classList.add("iq-aslSize-" + v);
      // You can expand with CSS rules later if desired.
    });
  }

  // Toggle
  if (btnToggle) {
    btnToggle.addEventListener("click", () => {
      aslEnabled = !aslEnabled;
      btnToggle.textContent = aslEnabled ? "ASL On" : "ASL Off";
      const panel = document.querySelector(".iq-aslPanel");
      if (panel) panel.style.display = aslEnabled ? "" : "none";

      if (!aslEnabled && aslVideo) {
        aslVideo.pause();
      } else if (aslEnabled) {
        // if main is playing, resume ASL
        if (sourceType === "youtube" && ytPlayer) {
          const state = ytPlayer.getPlayerState?.();
          if (state === 1) safePlay(aslVideo);
        } else if (mainVideo && !mainVideo.paused) {
          safePlay(aslVideo);
        }
      }
    });
  }

  // Position (below/side)
  if (selPosition) {
    selPosition.addEventListener("change", (e) => {
      applyPosition(e.target.value);
    });
  }

  function applyPosition(pos) {
    shell.classList.toggle("iq-asl--side", pos === "side");
    shell.classList.toggle("iq-asl--below", pos !== "side");
  }

  function safePlay(v) {
    if (!v) return;
    const p = v.play();
    if (p && typeof p.catch === "function") p.catch(() => {});
  }

  // --- Mux HLS path ---
  if (sourceType !== "youtube") {
    if (!mainVideo) return;

    const hlsUrl = muxPlaybackId
      ? `https://stream.mux.com/${muxPlaybackId}.m3u8`
      : null;

    if (!hlsUrl) return;

    // Attach HLS
    // Use native HLS on Safari/iOS, else hls.js
    if (mainVideo.canPlayType("application/vnd.apple.mpegurl")) {
      mainVideo.src = hlsUrl;
    } else {
      loadScriptOnce("https://cdn.jsdelivr.net/npm/hls.js@latest", () => {
        if (!window.Hls || !window.Hls.isSupported()) {
          mainVideo.src = hlsUrl;
          return;
        }
        const hls = new window.Hls({ enableWorker: true, lowLatencyMode: true });
        hls.loadSource(hlsUrl);
        hls.attachMedia(mainVideo);
      });
    }

    // Sync ASL to main video
    if (aslVideo) {
      mainVideo.addEventListener("play", () => { if (aslEnabled) syncAndPlay(aslVideo, mainVideo.currentTime); });
      mainVideo.addEventListener("pause", () => aslVideo.pause());
      mainVideo.addEventListener("seeking", () => { if (aslEnabled) sync(aslVideo, mainVideo.currentTime, true); });
      mainVideo.addEventListener("timeupdate", () => { if (aslEnabled) sync(aslVideo, mainVideo.currentTime, false); });
    }

    return;
  }

  // --- YouTube path ---
  if (!ytMount) return;

  const ytId = extractYouTubeId(youtubeUrl);
  if (!ytId) {
    ytMount.innerHTML = "<div style='padding:14px;color:#fff'>Missing YouTube URL.</div>";
    return;
  }

  // Load IFrame API
  loadScriptOnce("https://www.youtube.com/iframe_api", () => {
    // API calls a global when ready
    window.onYouTubeIframeAPIReady = function () {
      ytPlayer = new window.YT.Player("iq-yt", {
        videoId: ytId,
        playerVars: {
          modestbranding: 1,
          rel: 0,
          playsinline: 1
        },
        events: {
          onReady: () => {},
          onStateChange: onYTState
        }
      });
    };
  });

  function onYTState(e) {
    // 1 playing, 2 paused, 0 ended
    if (!aslVideo) return;

    if (e.data === 1) { // playing
      if (aslEnabled) {
        const t = ytPlayer.getCurrentTime();
        syncAndPlay(aslVideo, t);
        startYTSyncLoop();
      }
    } else if (e.data === 2 || e.data === 0) { // paused/ended
      aslVideo.pause();
      stopYTSyncLoop();
    }
  }

  function startYTSyncLoop() {
    stopYTSyncLoop();
    ytSyncTimer = setInterval(() => {
      if (!aslEnabled || !ytPlayer || !aslVideo) return;
      const state = ytPlayer.getPlayerState?.();
      if (state !== 1) return;
      const t = ytPlayer.getCurrentTime();
      sync(aslVideo, t, false);
    }, 250);
  }

  function stopYTSyncLoop() {
    if (ytSyncTimer) clearInterval(ytSyncTimer);
    ytSyncTimer = null;
  }

  function syncAndPlay(asl, t) {
    sync(asl, t, true);
    safePlay(asl);
  }

  function sync(asl, t, hard) {
    if (!asl || typeof t !== "number") return;
    const diff = Math.abs((asl.currentTime || 0) - t);
    // If drift is big, snap; else let it ride.
    if (hard || diff > 0.35) {
      try { asl.currentTime = t; } catch (_) {}
    }
  }

  function extractYouTubeId(url) {
    if (!url) return "";
    const m =
      url.match(/[?&]v=([^&#]+)/) ||
      url.match(/youtu\.be\/([^?&#]+)/) ||
      url.match(/youtube\.com\/embed\/([^?&#]+)/);
    return m ? m[1] : "";
  }

  function loadScriptOnce(src, cb) {
    if (document.querySelector(`script[src="${src}"]`)) {
      cb && cb();
      return;
    }
    const s = document.createElement("script");
    s.src = src;
    s.async = true;
    s.onload = () => cb && cb();
    document.head.appendChild(s);
  }
})();
