<?php include_once ROOT_PATH . "includes/components/dialog.php"; ?>
<style id="prompt-dialog-style">
  #prompt-template {
    display: none;
  }

  .prompt-form .prompt-message,
  .prompt-form .prompt-fields-container {
    margin: 20px 0px;
    width: 100%;
  }

  .prompt-form .prompt-fields-container td > * {
    display: block;
    margin: 10px 0px;
    padding: 10px;
    width: 100%;
    font-size: 13px;
    box-sizing: border-box;
  }

  .prompt-form .submit-button {
    float: right;
  }
</style>
<div id="prompt-template">
  <form class="prompt-form">
    <div class="prompt-message"></div>
    <table class="prompt-fields-container"></table>
    <button class="cancel-button" type="button"></button>
    <button class="submit-button" type="submit"></button>
  </form>
</div>
<script id="prompt-dialog-script">
  var promptTemplate = document.querySelector("#prompt-template");
  var promptForm = promptTemplate.querySelector(".prompt-form");

  function showPromptDialog({
    message = "",
    fields = [],
    cancel = "cancel",
    submit = "submit",
    callback = function () {},
    onCancel = function () {}
  }) {
    var content = promptForm.cloneNode(true);

    var fieldsContainerHTML = "";

    for (var i = 0; i < fields.length; i++) {
      var label = fields[i].label;

      fieldsContainerHTML += "<tr>"
        + (label
          ? "<td>" + (label ? label + ":" : "") + "</td><td>" + toHTMLString(fields[i]) + "</td>"
          : "<td colspan=\"2\">" + toHTMLString(fields[i]) + "</td>"
        )
      + "</tr>";
    }

    content.querySelector(".prompt-message").innerHTML = message;
    content.querySelector(".prompt-fields-container").innerHTML = fieldsContainerHTML;
    content.querySelector(".cancel-button").innerHTML = cancel;
    content.querySelector(".cancel-button").addEventListener("click", hideDialog);
    content.querySelector(".submit-button").innerHTML = submit;
    content.addEventListener("submit", function (event) {
      event.preventDefault();
      callback(serialize(this, false));
      hideDialog();
      return false;
    });

    showDialog({ content, onCancel });
  }
</script>
