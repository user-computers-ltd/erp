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
