(function () {
  var frame = document.getElementById('hero-video-player');
  var toggle = document.getElementById('hero-video-toggle');
  var fallback = document.getElementById('hero-video-fallback');
  var player;

  function showPlaying(playing) {
    toggle.textContent = playing ? 'Pause Video' : 'Play Video';
  }

  function showFallback() {
    toggle.hidden = true;
    fallback.hidden = false;
  }

  function initializePlayer() {
    player = new YT.Player(frame, {
      events: {
        onReady: function (event) {
          fallback.hidden = true;
          toggle.hidden = false;
          if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            event.target.mute();
            event.target.playVideo();
          }
        },
        onStateChange: function (event) {
          showPlaying(event.data === YT.PlayerState.PLAYING || event.data === YT.PlayerState.BUFFERING);
        },
        onAutoplayBlocked: function () {
          showPlaying(false);
        },
        onError: showFallback
      }
    });
  }

  toggle.addEventListener('click', function () {
    var state = player.getPlayerState();
    if (state === YT.PlayerState.PLAYING || state === YT.PlayerState.BUFFERING) {
      player.pauseVideo();
    } else {
      player.unMute();
      player.playVideo();
    }
  });

  var source = new URL(frame.src);
  source.searchParams.set('origin', window.location.origin);
  frame.src = source.href;

  window.onYouTubeIframeAPIReady = initializePlayer;
  var api = document.createElement('script');
  api.src = 'https://www.youtube.com/iframe_api';
  api.async = true;
  document.head.appendChild(api);
}());
