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
