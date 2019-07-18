<?php
  include_once ROOT_PATH . "includes/components/dialog.php";
  include_once ROOT_PATH . "includes/components/file-selector.php";
?>
<style id="import-csv-table-dialog-style">
  #import-csv-table-template {
    display: none;
  }

  .import-csv-form * {
    font-size: 13px;
  }

  .import-csv-form .submit-button {
    float: right;
  }

  .import-csv-form .import-csv-file-name,
  .import-csv-form .import-database-table-name {
    font-weight: bold;
  }

  .import-csv-form .import-csv-file-name,
  .import-csv-form .import-csv-column-count,
  .import-csv-form .import-csv-row-count,
  .import-csv-form .import-database-table-name {
    display: inline-block;
    font-size: 16px;
    margin-top: 10px;
    margin-right: 10px;
  }

  .import-csv-form .import-csv-table-wrapper,
  .import-csv-form .import-database-table-wrapper {
    margin: 10px 0px;
    width: 100%;
    overflow-x: auto;
  }

  .import-csv-form .import-csv-table,
  .import-csv-form .import-database-table {
    min-width: 100%;
  }

  .import-csv-form .import-csv-table th,
  .import-csv-form .import-database-table th {
    min-width: 50px;
  }

  .import-csv-form .import-database-table textarea,
  .import-csv-form .import-database-table .input-backdrop .input-highlights {
    line-height: 16px;
  }

  .import-csv-form .import-database-table textarea  {
    margin: 0px;
    padding: 0px;
    width: 100%;
    font-family: Courier, Courier New;
    resize: none;
    box-sizing: border-box;
    z-index: 2;
  }

  .import-csv-form .import-database-table .input-container {
    position: relative;
  }

  .import-csv-form .import-database-table .input-backdrop {
    position: absolute;
    width: 100%;
    height: 0px;
  }

  .import-csv-form .import-database-table .input-backdrop .input-highlights {
    height: 0px;
    white-space: pre-wrap;
    word-wrap: break-word;
  }

  .import-csv-form .import-database-table .input-backdrop .input-highlights mark {
    display: inline-block;
    background-color: #137ad0;
    color: #ffffff;
    border-radius: 2px;
    cursor: pointer;
  }
</style>
<div id="import-csv-table-template">
  <form class="import-csv-form">
    <div>
      <div class="import-csv-file-name"></div>
      <div class="import-csv-column-count"></div>
      <div class="import-csv-row-count"></div>
    </div>
    <div class="import-csv-table-wrapper">
      <table class="import-csv-table gridline"><thead></thead><tbody></tbody></table>
    </div>
    <div class="import-database-table-name"></div>
    <div class="import-database-table-wrapper">
      <table class="import-database-table gridline"><thead></thead><tbody></tbody></table>
    </div>
    <button class="cancel-button" type="button"></button>
    <button class="submit-button" type="submit"></button>
  </form>
</div>
<script id="import-csv-table-dialog-script">
  var importCSVTemplate = document.querySelector("#import-csv-table-template");
  var importCSVForm = importCSVTemplate.querySelector(".import-csv-form");

  function showImportCSVTableDialog({
    file,
    table = "",
    columns = [],
    cancel = "cancel",
    submit = "import",
    autoSubmit = false,
    callback = function() {},
    onCancel = function() {}
  }) {
    if (file) {
      readCSVFile(file, function(data) {
        var content = importCSVForm.cloneNode(true);

        if (data.columns.length > 0 && data.count > 0) {
          var fullyMatched = true;

          content.querySelector(".import-csv-file-name").innerHTML = file.name;
          content.querySelector(".import-csv-column-count").innerHTML = data.columns.length + " columns";
          content.querySelector(".import-csv-row-count").innerHTML = data.count + " rows";
          content.querySelector(".import-csv-table thead").innerHTML = generateTableHeaderHTML([data.columns]);
          content.querySelector(".import-csv-table tbody").innerHTML = generateTableBodyHTML(data.columns, data.samples);
          content.querySelector(".import-database-table-name").innerHTML = table;
          content.querySelector(".import-database-table thead").innerHTML = generateTableHeaderHTML([
            columns,
            columns.map(function(c) {
              fullyMatched = fullyMatched && data.columns.indexOf(c) !== -1;

              return "<textarea "
                + "name=\"" + c + "\""
                + "oninput=\"handleColumnInput(event, '" + c + "')\" "
              + ">"
                + (data.columns.indexOf(c) !== -1 ? "`" + c + "`" : "")
              + "</textarea>";
            })
          ]);
          content.querySelector(".import-database-table tbody").innerHTML = generateTableBodyHTML(
            columns,
            data.samples.map(function(s) {
              return columns.map(function(c) {
                return data.columns.indexOf(c) !== -1 ? s[data.columns.indexOf(c)] : "";
              });
            })
          );
          content.querySelector(".cancel-button").innerHTML = cancel;
          content.querySelector(".cancel-button").addEventListener("click", hideDialog);
          content.querySelector(".submit-button").innerHTML = submit;
          content.addEventListener("submit", function(event) {
            event.preventDefault();
            callback(serialize(this, false), file);
            hideDialog();
            return false;
          });

          if (autoSubmit && fullyMatched) {
            callback(serialize(content, false), file);
          } else {
            showDialog({ content, fullCover: true, onCancel });
          }
        } else {
          callback(serialize(content, false), file);
        }
      });
    } else {
      selectFile({
        callback: function (files) {
          showImportCSVTableDialog({ file: files[0], table, columns, submit, autoSubmit, callback, onCancel });
        }
      });
    }
  }

  function readCSVFile(file, callback = function() {}) {
    var columns = [];
    var samples = [];
    var count = 0;

    if (file) {
      var reader = new FileReader();

      reader.onload = function(event) {
        var result = event.target.result;
        var lines = result.substring(result.indexOf("\"") + 1, result.lastIndexOf("\"")).split(/\"\r\n\"|\"\n\"/);

        if (lines.length > 1) {
          columns = lines[0].split("\",\"");

          for (var i = 1; i <= 10 && i < lines.length; i++) {
            var rowValues = lines[i].replace(/,,/g, ",\"\",").replace(/,,/g, ",\"\",").split("\",\"");
            samples.push(rowValues);
          }

          count = lines.length - 1;
        }

        callback({ columns, samples, count });
      };

      reader.readAsText(file);
    } else {
      callback({ columns, samples, count });
    }
  }

  function generateTableHeaderHTML(headers) {
    var headerHTML = "";

    for (var i = 0; i < headers.length; i++) {
      var header = headers[i];

      headerHTML += "<tr>";

      for (var j = 0; j < header.length; j++) {
        headerHTML += "<th>" + header[j] + "</th>";
      }

      headerHTML += "</tr>";
    }

    return headerHTML;
  }

  function generateTableBodyHTML(headers, rows) {
    var bodyHTML = "";

    for (var i = 0; i < rows.length && i < 4; i++) {
      var row = rows[i];

      bodyHTML += "<tr>";

      for (var j = 0; j < row.length; j++) {
        var header = headers[j];
        bodyHTML += "<td data-column=\"" + header + "\">" + row[j] + "</td>";
      }

      bodyHTML += "</tr>";
    }

    if (rows.length > 4) {
      for (var i = 0; i < headers.length; i++) {
        bodyHTML += "<td>⋮</td>";
      }
    }

    return bodyHTML;
  }

  function handleColumnInput(event, column) {
    var textarea = event.target;
    var form = textarea.closest(".import-csv-form");
    var csvTableBody = form.querySelector(".import-csv-table tbody");
    var databaseTableBody = form.querySelector(".import-database-table tbody");
    var text = textarea.value;

    var cells = databaseTableBody.querySelectorAll("td[data-column=\"" + column + "\"]");

    for (var i = 0; i < cells.length; i++) {
      cells[i].innerHTML = text.replace(/`.*?`/g, function (x) {
        var lookup = x.replace(/`/g, "");
        var results = csvTableBody.querySelectorAll("td[data-column=\"" + lookup + "\"]");

        if (results.length > 0) {
          return results[i].innerHTML;
        } else {
          return x;
        }
      });
    }
  }
</script>
