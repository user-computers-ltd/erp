<style id="file-selector-style">
  #file-selector {
    display: none;
  }
</style>
<input type="file" hidden id="file-selector" />
<script id="file-selector-script">
  var fileSelector = document.querySelector("#file-selector");

  function selectFile({ multiple = false, accept = "", callback }) {
    fileSelector.accept = accept;
    fileSelector.multiple = multiple;
    fileSelector.onchange = function (event) {
      if (event.target && event.target.files) {
        var files = [];

        for (var i = 0; i < event.target.files.length; i++) {
          files.push(event.target.files[i]);
        }

        callback(files);
      }
    }
    fileSelector.click();
  }
</script>
