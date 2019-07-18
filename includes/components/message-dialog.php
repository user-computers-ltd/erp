<?php include_once ROOT_PATH . "includes/components/dialog.php"; ?>
<style id="message-dialog-style">
  #message-template {
    display: none;
  }

  .message-form .message-content {
    padding: 20px 0px;
  }

  .message-form .submit-button {
    float: right;
  }
</style>
<div id="message-template">
  <form class="message-form">
    <div class="message-content"></div>
    <button class="submit-button" type="button"></button>
  </form>
</div>
<script id="message-dialog-script">
  var messageTemplate = document.querySelector("#message-template");
  var messageForm = messageTemplate.querySelector(".message-form");

  function showMessageDialog({
    message = "",
    submit = "close",
    callback = function () {}
  }) {
    var content = messageForm.cloneNode(true);
    content.querySelector(".message-content").innerHTML = message;
    content.querySelector(".submit-button").addEventListener("click", hideDialog);
    content.querySelector(".submit-button").innerHTML = submit;

    showDialog({ content, onCancel: callback });
  }
</script>
