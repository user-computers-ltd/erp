var nextBackupElement = document.querySelector(".system-next-backup");

function backupDatabase(database) {
  sendPostRequest({
    url: apiURL,
    loadingMessage: "Backing up `" + database + "`...",
    reloadPage: true,
    data: {
      action: "backup-database",
      database: database
    }
  });
}

function recoverFromBackup(database, time) {
  showConfirmDialog({
    message:
      "Are you sure you want to recover the current database from backup at " +
      time +
      "?",
    submit: "recover",
    callback: function() {
      sendPostRequest({
        url: apiURL,
        loadingMessage: "Recovering from backup...",
        reloadPage: true,
        data: {
          action: "recover-database",
          database: systemName,
          backup: database
        }
      });
    }
  });
}

function deleteBackup(database, time) {
  showConfirmDialog({
    message: "Are you sure you want to delete backup at " + time + "?",
    submit: "delete",
    callback: function() {
      sendPostRequest({
        url: apiURL,
        loadingMessage: "Deleting backup...",
        reloadPage: true,
        data: {
          action: "delete-database",
          database: database
        }
      });
    }
  });
}

function exportFromBackup(database, time) {
  sendPostRequest({
    url: apiURL,
    loadingMessage: "Exporting data into CSV from backup at " + time + "...",
    respondFile: true,
    data: {
      action: "export-database",
      database: database
    }
  });
}

if (backupCron) {
  var variance = 0;

  if (cronVariance) {
    if (cronVariance["day"]) {
      variance += cronVariance["day"] * 1000 * 60 * 60 * 24;
    }

    if (cronVariance["hour"]) {
      variance += cronVariance["hour"] * 1000 * 60 * 60;
    }

    if (cronVariance["minute"]) {
      variance += cronVariance["minute"] * 1000 * 60;
    }

    if (cronVariance["second"]) {
      variance += cronVariance["second"] * 1000;
    }
  }

  var lastBackup = latestBackupTime && new Date(latestBackupTime);
  var earliestBackupAvaiable =
    lastBackup && new Date(lastBackup.getTime() + variance);
  var nextBackup = later
    .schedule(later.parse.cron(backupCron))
    .next(1, earliestBackupAvaiable);
  console.log(nextBackup);

  if (nextBackup) {
    var countdownTimer = setInterval(function() {
      var diff = nextBackup - new Date();

      if (diff > 0) {
        var days = Math.floor(diff / 1000 / 60 / 60 / 24);
        diff -= days * 1000 * 60 * 60 * 24;
        var hours = Math.floor(diff / 1000 / 60 / 60);
        diff -= hours * 1000 * 60 * 60;
        var minutes = Math.floor(diff / 1000 / 60);
        diff -= minutes * 1000 * 60;
        var seconds = Math.floor(diff / 1000);

        nextBackupElement.innerHTML = `Backup scheduled in ${
          days > 1 ? days + " days and " : days > 0 ? days + " day and " : ""
        } ${doubleDigit(hours)}:${doubleDigit(minutes)}:${doubleDigit(
          seconds
        )}`;
      } else {
        nextBackupElement.innerHTML = "Backing up...";
        clearInterval(countdownTimer);
        sendPostRequest({
          url: apiURL,
          loadingMessage: "Backing up `" + systemName + "`...",
          reloadPage: true,
          data: {
            action: "schedule-backup-database",
            database: systemName
          }
        });
      }
    }, 1000);
  }
} else {
  nextBackupElement.innerHTML = "No scheduled backup";
}
