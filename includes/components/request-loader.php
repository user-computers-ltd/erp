<?php include_once ROOT_PATH . "includes/components/screen-progress.php"; ?>
<script id="request-loader-script">
  function sendPostRequest(settings) {
    var loadingMessage = settings.loadingMessage || "";
    var reloadPage = settings.reloadPage ? true : false;
    var hideErrorMessages = settings.hideErrorMessages ? true : false;
    var callback = settings.callback || function () {};
    var onError = settings.onError || function () {};

    toggleScreenProgress(true, loadingMessage);

    post({
      url: settings.url,
      data: settings.data,
      urlEncoded: settings.urlEncoded,
      respondFile: settings.respondFile,
      resolve: function (data) {
        if (reloadPage) {
          window.location.reload();
        } else {
          toggleScreenProgress(false);
          callback(data);
        }
      },
      reject: function (message) {
        if (!hideErrorMessages) {
          showMessageDialog({ message });
        }

        toggleScreenProgress(false);
        onError(message);
      }
    });
  }
</script>
