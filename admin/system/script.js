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
          database: system["name"],
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

var backupCron = system["backup-cron"];
var cronVariance = system["backup-cron-variance"];

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

  var lastBackup = new Date(system["latest-backup-datetime"]);
  var nextBackups = later.schedule(later.parse.cron(backupCron)).next(2);
  var nextBackup =
    lastBackup && nextBackups[0] - lastBackup <= variance
      ? nextBackups[1]
      : nextBackups[0];

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
          days > 0 ? days + "d" : ""
        } ${doubleDigit(hours)}:${doubleDigit(minutes)}:${doubleDigit(
          seconds
        )}`;
      } else {
        nextBackupElement.innerHTML = "Backing up...";
        clearInterval(countdownTimer);
        sendPostRequest({
          url: apiURL,
          loadingMessage: "Backing up `" + system["name"] + "`...",
          reloadPage: true,
          data: {
            action: "schedule-backup-database",
            database: system["name"]
          }
        });
      }
    }, 1000);
  }
} else {
  nextBackupElement.innerHTML = "No scheduled backup";
}
