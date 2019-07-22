function toURLParams(object) {
  var urlParams = [];

  for (var key in object) {
    var value = object[key];

    if (typeof value !== "undefined") {
      if (value.constructor === Array) {
        value.forEach(function(v) {
          urlParams.push(key + "[]=" + v);
        });
      } else {
        urlParams.push(key + "=" + value);
      }
    }
  }

  return urlParams.join("&");
}

function ajax(settings = {}) {
  var url = settings.url;
  var data = settings.data;
  var params = settings.params;
  var method = settings.method;
  var urlEncoded = settings.urlEncoded;
  var respondFile = settings.respondFile;
  var resolve = settings.resolve || function() {};
  var reject = settings.reject || function() {};

  if (params) {
    url += "?" + toURLParams(params);
  }

  var xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = function() {
    if (xhttp.readyState === 4) {
      if (xhttp.status === 200) {
        var response = true;
        var disposition = xhttp.getResponseHeader("content-disposition");
        var type = xhttp.getResponseHeader("content-type");

        if (disposition && disposition.indexOf("attachment") !== -1) {
          var filename = disposition.substring(
            disposition.indexOf("filename=") + 9
          );
          var filename = filename ? filename : "file";
          var link = document.createElement("a");
          link.href = window.URL.createObjectURL(
            new Blob([xhttp.response], { type: type })
          );
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } else {
          try {
            response = JSON.parse(xhttp.responseText);
          } catch (error) {}
        }

        resolve(response);
      } else {
        if (respondFile && xhttp.response) {
          var reader = new FileReader();

          reader.onload = function(event) {
            reject(event.target.result);
          };

          reader.readAsText(xhttp.response);
        } else if (xhttp.responseText) {
          reject(xhttp.responseText);
        } else {
          reject("error with no proper response");
        }
      }
    }
  };

  xhttp.open(method, url, true);

  if (urlEncoded) {
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    data = toURLParams(data);
  }

  if (respondFile) {
    xhttp.responseType = "blob";
  }

  xhttp.send(data);
}

function get(settings) {
  settings.method = "get";
  settings.urlEncoded =
    typeof settings.urlEncoded !== "undefined" ? settings.urlEncoded : false;
  settings.respondFile =
    typeof settings.respondFile !== "undefined" ? settings.respondFile : false;

  ajax(settings);
}

function post(settings) {
  settings.method = "post";
  settings.urlEncoded =
    typeof settings.urlEncoded !== "undefined" ? settings.urlEncoded : true;
  settings.respondFile =
    typeof settings.respondFile !== "undefined" ? settings.respondFile : false;

  ajax(settings);
}

function hasClass(element, className) {
  return element.className.split(" ").indexOf(className) !== -1;
}

function toggleClass(element, className, toggle) {
  var classes = element.className.split(" ");
  var index = classes.indexOf(className);

  if (toggle === true && index === -1) {
    classes.push(className);
  } else if (toggle === false && index !== -1) {
    classes.splice(index, 1);
  } else if (toggle !== true && toggle !== false) {
    if (index === -1) {
      classes.push(className);
    } else if (index !== -1) {
      classes.splice(index, 1);
    }
  }

  element.className = classes.join(" ");
}

function serialize(form, toString = true) {
  if (!form || form.nodeName !== "FORM") {
    return;
  }

  var query = [];
  var processField = function(fields, field) {
    fields.push(field.name + "=" + encodeURIComponent(field.value));
  };

  if (!toString) {
    query = {};
    processField = function(fields, field) {
      if (field.name.indexOf("[]") === field.name.length - 2) {
        if (!fields[field.name]) {
          fields[field.name] = [];
        }
        fields[field.name].push(encodeURIComponent(field.value));
      } else {
        fields[field.name] = encodeURIComponent(field.value);
      }
    };
  }

  for (var i = form.elements.length - 1; i >= 0; i--) {
    var element = form.elements[i];
    if (element.name === "" || element.disabled) {
      continue;
    }

    switch (element.nodeName) {
      case "INPUT":
        switch (element.type) {
          case "text":
          case "hidden":
          case "password":
          case "button":
          case "reset":
          case "submit":
          case "number":
          case "date":
            processField(query, element);
            break;
          case "checkbox":
          case "radio":
            if (element.checked) {
              processField(query, element);
            }
            break;
          case "file":
            break;
        }
        break;
      case "TEXTAREA":
        processField(query, element);
        break;
      case "SELECT":
        switch (element.type) {
          case "select-one":
            processField(query, element);
            break;
          case "select-multiple":
            for (var j = element.options.length - 1; j >= 0; j--) {
              if (element.options[j].selected) {
                element.value = element.options[j].value;
                processField(query, element);
              }
            }
            break;
        }
        break;
      case "BUTTON":
        switch (element.type) {
          case "reset":
          case "submit":
          case "button":
            processField(query, element);
            break;
        }
        break;
    }
  }

  return toString ? query.join("&") : query;
}

function downloadTextFile(filename, text) {
  var element = document.createElement("a");
  var content = "data:text/plain;charset=utf-8," + encodeURIComponent(text);
  element.setAttribute("href", content);
  element.setAttribute("download", filename);

  element.style.display = "none";
  document.body.appendChild(element);

  element.click();

  document.body.removeChild(element);
}

function doubleDigit(n) {
  return n < 10 ? "0" + n : n;
}

function getTime(dateString) {
  if (dateString) {
    var parts = dateString.split("-");
    var date = parts[0];
    var month = parts[1] - 1;
    var year = parts[2];

    return new Date(year, month, date).getTime();
  } else {
    return 0;
  }
}

function formatDate(dateTime) {
  return (
    dateTime.getFullYear() +
    "-" +
    doubleDigit(dateTime.getMonth() + 1) +
    "-" +
    doubleDigit(dateTime.getDate())
  );
}

function setTableSortable(table) {
  var headerColumns = table.querySelectorAll("thead tr th");
  var rows = table.querySelectorAll("tbody tr");

  for (var i = 0; i < rows.length - 1; i++) {
    for (var j = 0; j < headerColumns.length; j++) {
      var cell = rows[i].getElementsByTagName("td")[j];

      if (cell) {
        var value = cell.innerText.toLowerCase();
        var dateMatches = value.match(/([0-9]+)\-([0-9]+)\-([0-9]+)/g) || [];

        if (dateMatches.length > 0) {
          value = dateMatches[0]
            .split("-")
            .reverse()
            .join("");
        } else if (hasClass(headerColumns[j], "number")) {
          value = parseFloat(value.replace(",", "")) || 0;
        }

        cell.dataset.sortvalue = value;
      }
    }
  }

  for (var i = 0; i < headerColumns.length; i++) {
    toggleClass(headerColumns[i], "sort-column", true);

    var s = function(index) {
      return function() {
        if (this === event.target) {
          sortTable(table, index);
        }
      };
    };
    headerColumns[i].addEventListener("click", s(i));
  }
}

function sortTable(table, columnIndex) {
  var headerColumns = table.querySelectorAll("thead tr th");
  var tbody = table.querySelector("tbody");
  var rowElements = tbody.querySelectorAll("tr");
  var rows = [];
  var sortedAsc = hasClass(headerColumns[columnIndex], "sorted-asc");

  var parseValue = hasClass(headerColumns[columnIndex], "number")
    ? parseFloat
    : function(v) {
        return v;
      };

  for (var i = 0; i < rowElements.length; i++) {
    rows.push(rowElements[i]);
  }

  for (var i = 0; i < headerColumns.length; i++) {
    if (columnIndex === i) {
      toggleClass(headerColumns[i], "sorted-asc", !sortedAsc);
      toggleClass(headerColumns[i], "sorted-desc", sortedAsc);
    } else {
      toggleClass(headerColumns[i], "sorted-asc", false);
      toggleClass(headerColumns[i], "sorted-desc", false);
    }
  }

  rows.sort(function(a, b) {
    var x = a.getElementsByTagName("td")[columnIndex];
    var y = b.getElementsByTagName("td")[columnIndex];

    if (x && y) {
      var xValue = parseValue(x.dataset.sortvalue);
      var yValue = parseValue(y.dataset.sortvalue);

      return (!sortedAsc && xValue > yValue) || (sortedAsc && xValue < yValue);
    }
  });

  tbody.innerHTML = "";

  for (var i = 0; i < rows.length; i++) {
    tbody.appendChild(rows[i]);
  }
}

window.addEventListener("load", function() {
  // Setup sortable tables.
  var tables = document.querySelectorAll("table.sortable");

  for (var i = 0; i < tables.length; i++) {
    setTableSortable(tables[i]);
  }
});

// Prevent scrolling on numerical input fields.
window.addEventListener(
  "focus",
  function(event) {
    var target = event.target;

    if (target.matches && target.matches('input[type="number"]')) {
      var wheelHandler = function(event) {
        event.preventDefault();
      };

      var blurHandler = function(event) {
        event.target.removeEventListener("wheel", wheelHandler);
        event.target.removeEventListener("blur", blurHandler);
      };

      target.addEventListener("wheel", wheelHandler);
      target.addEventListener("blur", blurHandler);
    }
  },
  true
);

// Auto expand on textarea.
window.addEventListener(
  "input",
  function(event) {
    var target = event.target;

    if (target.tagName.toLowerCase() === "textarea") {
      target.style.height = "inherit";

      var computed = window.getComputedStyle(target);

      var height =
        parseInt(computed.getPropertyValue("border-top-width"), 10) +
        parseInt(computed.getPropertyValue("padding-top"), 10) +
        target.scrollHeight +
        parseInt(computed.getPropertyValue("padding-bottom"), 10) +
        parseInt(computed.getPropertyValue("border-bottom-width"), 10);

      target.style.height = height + "px";
    }
  },
  false
);

if (!Element.prototype.matches) {
  Element.prototype.matches =
    Element.prototype.msMatchesSelector ||
    Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
  Element.prototype.closest = function(s) {
    var el = this;

    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);
    return null;
  };
}
