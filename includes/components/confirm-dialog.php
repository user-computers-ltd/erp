<?php include_once ROOT_PATH . "includes/components/dialog.php"; ?>
<style id="confirm-dialog-style">
  #confirm-template {
    display: none;
  }

  .confirm-form .confirm-message {
    padding: 20px 0px;
  }

  .confirm-form .submit-button {
    float: right;
  }
</style>
<div id="confirm-template">
  <form class="confirm-form">
    <div class="confirm-message"></div>
    <button class="cancel-button" type="button"></button>
    <button class="submit-button" type="submit"></button>
  </form>
</div>
<script id="confirm-dialog-script">
  var confirmTemplate = document.querySelector("#confirm-template");
  var confirmForm = confirmTemplate.querySelector(".confirm-form");

  function showConfirmDialog({
    message = "",
    cancel = "cancel",
    submit = "confirm",
    callback = function () {},
    onCancel = function () {}
  }) {
    var content = confirmForm.cloneNode(true);

    content.querySelector(".confirm-message").innerHTML = message;
    content.querySelector(".cancel-button").innerHTML = cancel;
    content.querySelector(".cancel-button").addEventListener("click", hideDialog);
    content.querySelector(".submit-button").innerHTML = submit;
    content.addEventListener("submit", function (event) {
      event.preventDefault();
      callback();
      hideDialog();
      return false;
    });
    
    showDialog({ content, onCancel });
  }
</script>
