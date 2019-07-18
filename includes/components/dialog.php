<style id="dialog-style">
  #dialog-overlay {
    position: fixed;
    padding: 20px;
    bottom: 100%;
    left: 0px;
    width: 100%;
    height: 100%;
    text-align: center;
    box-sizing: border-box;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 100;
    opacity: 0;
    transition: bottom 0s 0.1s, opacity 0.1s;
    overflow: auto;
  }

  #dialog-overlay.show {
    bottom: 0px;
    opacity: 1;
    transition: bottom 0s 0s, opacity 0.1s;
  }

  #dialog-overlay .dialog {
    display: inline-block;
    position: relative;
    margin-bottom: 20px;
    padding: 20px;
    max-width: 100%;
    text-align: left;
    border-radius: 5px;
    box-sizing: border-box;
    background-color: white;
  }

  #dialog-overlay .dialog .close-button {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 20px;
    height: 20px;
    font-size: 24px;
    text-align: center;
    line-height: 20px;
    cursor: pointer;
  }
</style>
<div id="dialog-overlay">
  <div class="dialog">
    <div class="close-button">&times;</div>
    <div class="content"></div>
  </div>
</div>
<script id="dialog-script">
  var dialogOverlay = document.querySelector("#dialog-overlay");
  var closeButton = dialogOverlay.querySelector(".close-button");
  var contentElement = dialogOverlay.querySelector(".content");

  function showDialog({ content = "", onCancel = function () {} }) {
    contentElement.innerHTML = "";
    toggleClass(dialogOverlay, "show", true);

    if (typeof content === "object") {
      contentElement.appendChild(content);
    } else {
      contentElement.innerHTML = content;
    }

    window.dialogCancelCallback = onCancel;
  }

  function hideDialog(event) {
    if (!event || event.target === this) {
      toggleClass(dialogOverlay, "show", false);

      window.dialogCancelCallback && window.dialogCancelCallback();
    }
  }

  dialogOverlay.addEventListener("click", hideDialog);
  closeButton.addEventListener("click", hideDialog);
</script>
