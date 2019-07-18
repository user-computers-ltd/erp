var queryInput = document.querySelector(".database-query textarea");

function queryDatabase() {
  var sql = encodeURIComponent(queryInput.value.trim());
  var reloadPage = !sql.startsWith("SELECT");

  if (sql) {
    sendPostRequest({
      url: apiURL,
      loadingMessage: "Executing statement...",
      reloadPage: reloadPage,
      data: {
        action: "query-database",
        database: database,
        sql: sql
      },
      callback: function(data) {
        showMessageDialog({
          message: generateDatabaseQueryResultTableHTML(data)
        });
      }
    });
  }
}

function createTable() {
  showPromptDialog({
    message: "Please enter table name:",
    submit: "create",
    fields: [
      {
        element: "input",
        name: "table",
        placeholder: "Table name",
        required: true
      }
    ],
    callback: function(fields) {
      var table = fields["table"].trim();

      if (table) {
        sendPostRequest({
          url: apiURL,
          loadingMessage: "Creating `" + table + "`...",
          reloadPage: true,
          data: {
            action: "create-table",
            database: database,
            table: table
          }
        });
      }
    }
  });
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

function importTable(table) {
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

function generateDatabaseQueryResultTableHTML(data) {
  var tableHTML =
    '<div class="database-query-results"><table class="gridline"><thead>';

  for (var i = 0; i < data.length; i++) {
    var row = data[i];
    var columns = Object.keys(row);

    tableHTML += "<tr>";

    if (i === 0) {
      for (var j = 0; j < columns.length; j++) {
        tableHTML += "<th>" + columns[j] + "</th>";
      }

      tableHTML += "</tr></thead><tbody><tr>";
    }

    for (var j = 0; j < columns.length; j++) {
      tableHTML += "<td>" + row[columns[j]] + "</td>";
    }

    tableHTML += "</tr>";
  }

  tableHTML += "</tbody></table></div>";

  return tableHTML;
}
