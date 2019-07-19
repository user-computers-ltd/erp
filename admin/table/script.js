var tabcontents = document.querySelectorAll(".tab-content");
var tabs = document.querySelectorAll(".tab");

function openTab(event, tabName) {
  for (var i = 0; i < tabcontents.length; i++) {
    toggleClass(tabcontents[i], "show", false);
  }

  for (var i = 0; i < tabs.length; i++) {
    toggleClass(tabs[i], "active", false);
  }

  toggleClass(document.querySelector(".table-" + tabName), "show", true);
  toggleClass(event.target, "active", true);
}

function deleteTable(table) {
  showConfirmDialog({
    message: "Are you sure you want to delete `" + table + "`?",
    submit: "delete",
    callback: function() {
      sendPostRequest({
        url: apiURL,
        loadingMessage: "Deleting `" + table + "`...",
        reloadPage: true,
        data: {
          action: "delete-table",
          database: database,
          table: table
        }
      });
    }
  });
}

function copyTable(table) {
  showPromptDialog({
    message: "Please enter new table name:",
    submit: "copy",
    fields: [
      {
        element: "input",
        name: "table",
        placeholder: "Table name",
        required: true
      }
    ],
    callback: function(fields) {
      var newTable = fields["table"].trim();

      if (newTable) {
        sendPostRequest({
          url: apiURL,
          loadingMessage: "Copying `" + table + "` to `" + newTable + "`...",
          reloadPage: true,
          data: {
            action: "copy-table",
            database: database,
            table1: newTable,
            table2: table
          }
        });
      }
    }
  });
}

function clearTable(table) {
  showConfirmDialog({
    message: "Are you sure you want to remove all data from `" + table + "`?",
    submit: "remove all data",
    callback: function() {
      sendPostRequest({
        url: apiURL,
        loadingMessage: "Removing all data from `" + table + "`...",
        reloadPage: true,
        data: {
          action: "clear-table",
          database: database,
          table: table
        }
      });
    }
  });
}

function clearImportTable(table, columns) {
  showConfirmDialog({
    message: "Are you sure you want to remove all data from `" + table + "`?",
    submit: "remove data and import CSV",
    callback: function() {
      importTableFromCSV(table, columns, "clear-import-table");
    }
  });
}

function importTable(table, columns) {
  importTableFromCSV(table, columns, "import-table");
}

function exportTable(table) {
  sendPostRequest({
    url: apiURL,
    loadingMessage: "Exporting data into CSV from `" + table + "`...",
    respondFile: true,
    data: {
      action: "export-table",
      database: database,
      table: table
    }
  });
}

function restartTable(table, confirmOverwrite = false) {
  var sendRestartRequest = function() {
    sendPostRequest({
      url: apiURL,
      loadingMessage: "Restarting `" + table + "` from system settings...",
      reloadPage: true,
      data: {
        action: "restart-table",
        system: database,
        table: table,
        overwrite: confirmOverwrite
      }
    });
  };

  if (confirmOverwrite) {
    showConfirmDialog({
      message:
        "Restarting `" +
        table +
        "` will reset all data and table structure.<br/><br/>" +
        "Are you sure you want to restart `" +
        table +
        "` from system settings?",
      submit: "restart from system settings",
      callback: sendRestartRequest
    });
  } else {
    sendRestartRequest();
  }
}

function importTableFromCSV(table, columns, action) {
  showImportCSVTableDialog({
    table: table,
    columns: columns,
    autoSubmit: true,
    callback: function(data, importFile) {
      var formData = new FormData();

      formData.append("action", action);
      formData.append("database", database);
      formData.append("table", table);
      formData.append("import", importFile);

      var columns = Object.keys(data);

      for (var i = 0; i < columns.length; i++) {
        formData.append("column[]", columns[i]);
        formData.append("value[]", data[columns[i]]);
      }

      sendPostRequest({
        url: apiURL,
        loadingMessage:
          "Importing `" + importFile.name + "` to `" + table + "`...",
        urlEncoded: false,
        reloadPage: true,
        data: formData
      });
    }
  });
}

function exportResults() {
  sendPostRequest({
    url: apiURL,
    loadingMessage: "Exporting results into CSV...",
    respondFile: true,
    data: {
      action: "export-query",
      database: database,
      sql: encodeURI(sql)
    }
  });
}

function deleteColumn(column) {
  showConfirmDialog({
    message:
      "Are you sure you want to delete column `" +
      column +
      "` from `" +
      table +
      "`?",
    submit: "delete",
    callback: function() {
      sendPostRequest({
        url: apiURL,
        loadingMessage:
          "Deleting column `" + column + "` from `" + table + "`...",
        reloadPage: true,
        data: {
          action: "delete-column",
          database: database,
          table: table,
          column: column
        }
      });
    }
  });
}
