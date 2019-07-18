function createDatabase() {
  showPromptDialog({
    message: "Please enter database name:",
    submit: "create",
    fields: [
      {
        element: "input",
        name: "database",
        placeholder: "Database name",
        required: true
      }
    ],
    callback: function(fields) {
      var database = fields["database"].trim();

      if (database) {
        sendPostRequest({
          url: apiURL,
          loadingMessage: "Creating `" + database + "`...",
          reloadPage: true,
          data: {
            action: "create-database",
            database: database
          }
        });
      }
    }
  });
}

function deleteDatabase(database) {
  showConfirmDialog({
    message: "Are you sure you want to delete `" + database + "`?",
    submit: "delete",
    callback: function() {
      sendPostRequest({
        url: apiURL,
        loadingMessage: "Deleting `" + database + "`...",
        reloadPage: true,
        data: {
          action: "delete-database",
          database: database
        }
      });
    }
  });
}

function copyDatabase(database) {
  showPromptDialog({
    message: "Please enter new database name:",
    submit: "copy",
    fields: [
      {
        element: "input",
        name: "database",
        placeholder: "Database name",
        required: true
      }
    ],
    callback: function(fields) {
      var newDatabase = fields["database"].trim();

      if (newDatabase) {
        sendPostRequest({
          url: apiURL,
          loadingMessage:
            "Copying `" + database + "` to `" + newDatabase + "`...",
          reloadPage: true,
          data: {
            action: "copy-database",
            database1: newDatabase,
            database2: database
          }
        });
      }
    }
  });
}

function clearDatabase(database) {
  showConfirmDialog({
    message:
      "Are you sure you want to remove all data from `" + database + "`?",
    submit: "remove all data",
    callback: function() {
      sendPostRequest({
        url: apiURL,
        loadingMessage: "Removing all data from `" + database + "`...",
        reloadPage: true,
        data: {
          action: "clear-database",
          database: database
        }
      });
    }
  });
}

function clearImportDatabase(database) {
  showConfirmDialog({
    message:
      "Are you sure you want to remove all data from `" + database + "`?",
    submit: "remove data and import CSV",
    callback: function() {
      selectFile({
        multiple: true,
        callback: function(files) {
          importDatabaseFromCSV(database, files, "clear-import-table");
        }
      });
    }
  });
}

function importDatabase(database) {
  selectFile({
    multiple: true,
    callback: function(files) {
      importDatabaseFromCSV(database, files, "import-table");
    }
  });
}

function exportDatabase(database) {
  sendPostRequest({
    url: apiURL,
    loadingMessage: "Exporting data into CSV from `" + database + "`...",
    respondFile: true,
    data: {
      action: "export-database",
      database: database
    }
  });
}

function restartDatabase(database, confirmOverwrite = false) {
  var sendRestartRequest = function() {
    sendPostRequest({
      url: apiURL,
      loadingMessage: "Restarting `" + database + "` from system settings...",
      reloadPage: true,
      data: {
        action: "restart-database",
        system: database,
        overwrite: confirmOverwrite
      }
    });
  };

  if (confirmOverwrite) {
    showConfirmDialog({
      message:
        "Restarting `" +
        database +
        "` will reset all data and table structures.<br/><br/>" +
        "Are you sure you want to restart `" +
        database +
        "` from system settings?",
      submit: "restart from system settings",
      callback: sendRestartRequest
    });
  } else {
    sendRestartRequest();
  }
}

function importDatabaseFromCSV(database, files, action) {
  var tables = databases[database];
  var fileCount = files.length;
  var failedTables = [];
  var missingTables = [];
  var abortedFiles = [];

  function chainImportFile(file) {
    if (file) {
      var table = file["name"].split(".")[0];
      var columns = tables[table];

      if (table && columns) {
        showImportCSVTableDialog({
          file: file,
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

            if (columns.length > 0) {
              sendPostRequest({
                url: apiURL,
                loadingMessage:
                  "Importing `" + importFile.name + "` to `" + table + "`...",
                urlEncoded: false,
                hideErrorMessages: true,
                callback: function() {
                  chainImportFile(files.shift());
                },
                onError: function() {
                  failedTables.push(table);
                  chainImportFile(files.shift());
                },
                data: formData
              });
            } else {
              chainImportFile(files.shift());
            }
          },
          onCancel: function() {
            abortedFiles.push(table);
            chainImportFile(files.shift());
          }
        });
      } else {
        missingTables.push(table);
        chainImportFile(files.shift());
      }
    } else {
      var message = "";

      if (failedTables.length > 0) {
        message +=
          "Failed to import CSV for the following tables:<br/><ul>" +
          failedTables
            .map(function(t) {
              return "<li>" + t + "</li>";
            })
            .join("") +
          "</ul>";
      }

      if (missingTables.length > 0) {
        message +=
          "Files are not imported due to missing tables:<br/><ul>" +
          missingTables
            .map(function(t) {
              return "<li>" + t + ".csv</li>";
            })
            .join("") +
          "</ul>";
      }

      if (abortedFiles.length > 0) {
        message +=
          "Files are not import by cancellation:<br/><ul>" +
          abortedFiles
            .map(function(t) {
              return "<li>" + t + ".csv</li>";
            })
            .join("") +
          "</ul>";
      }

      showMessageDialog({
        message: message
          ? message
          : fileCount +
            " CSV files has been imported successfully into `" +
            database +
            "`."
      });
    }
  }

  chainImportFile(files.shift());
}
