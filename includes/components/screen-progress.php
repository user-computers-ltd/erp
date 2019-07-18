<style id="screen-progress-style">
  #screen-progress-overlay {
    position: fixed;
    bottom: 100%;
    left: 0px;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 100;
    opacity: 0;
    transition: bottom 0s 0.3s, opacity 0.2s;
  }

  #screen-progress-overlay.show {
    bottom: 0px;
    opacity: 1;
    transition: bottom 0s 0s, opacity 0.2s;
  }

  #screen-progress-overlay .screen-progress-wrapper {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  #screen-progress-overlay .screen-progress-wrapper .screen-progress {
    border: 10px solid #c0c0c0;
    border-left-color: #ffffff;
    -webkit-animation: screen-progress-spin 1s infinite linear;
    animation: screen-progress-spin 1s infinite linear;
  }

  #screen-progress-overlay .screen-progress-wrapper .screen-progress,
  #screen-progress-overlay .screen-progress-wrapper .screen-progress:after {
    margin: auto;
    width: 80px;
    height: 80px;
    border-radius: 50%;
  }

  #screen-progress-overlay .screen-progress-wrapper .screen-progress-message {
    margin-top: 20px;
    width: 300px;
    max-width: 100%;
    text-align: center;
    color: #ffffff;
    text-shadow: 0px 1px 1px #000000;
  }

  @-webkit-keyframes screen-progress-spin {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }
    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }

  @keyframes screen-progress-spin {
    0% {
      -webkit-transform: rotate(0deg);
      transform: rotate(0deg);
    }
    100% {
      -webkit-transform: rotate(360deg);
      transform: rotate(360deg);
    }
  }
</style>
<div id="screen-progress-overlay">
  <div class="screen-progress-wrapper">
    <div class="screen-progress"></div>
    <div class="screen-progress-message"></div>
  </div>
</div>
<script id="screen-progress-script">
  var screenProgressOverlay = document.querySelector("#screen-progress-overlay");
  var screenProgressMessage = screenProgressOverlay.querySelector(".screen-progress-message");

  function toggleScreenProgress(toggle, message = "") {
    toggleClass(screenProgressOverlay, "show", toggle);

    if (toggle) {
      screenProgressMessage.innerHTML = message;
    }
  }
</script>
