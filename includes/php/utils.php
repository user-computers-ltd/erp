<?php
  include_once "config.php";

  define("PROTOCAL", isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https" : "http");
  define("CURRENT_URL", urldecode(PROTOCAL . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"));
  define("BASE_URL", defined("ROOT_URL") ? ROOT_URL . "erp/" : "");
  define("RELATIONAL_BASE_URL", defined("ROOT_URL") ? ROOT_URL . "erp/" : ROOT_PATH);

  $isErrorPage = strpos(CURRENT_URL, "error.php") === strlen(CURRENT_URL) - strlen("error.php");

  if (!$isErrorPage) {
    unset($_SESSION["error"]);

    if (!defined("ROOT_URL")) {
      sendErrorPage(array(
        title => "Missing configuration file",
        content => "
          The ERP framework requires a configuration file to operate.
          Please contact your administrator to check if the file exists in following path:
          <pre>includes/php/config.php</pre>
          Note: Please make sure the followings are set:
          <ul>
            <li><code>ROOT_URL</code></li>
            <li><code>MYSQL_HOST</code></li>
            <li><code>MYSQL_USER</code></li>
            <li><code>MYSQL_PASSWORD</code></li>
            <li><code>TEMP_DIRECTORY</code></li>
          </ul>
        "
      ));
    }
  }

  $columnTypes = array(
    "INT",
    "DECIMAL",
    "FLOAT",
    "DOUBLE",
    "BOOLEAN",
    "CHAR",
    "VARCHAR",
    "TEXT",
    "DATE",
    "DATETIME",
    "TIMESTAMP",
    "ENUM"
  );

  function assigned($data) {
    return isset($data) && $data != "";
  }

  function sanitize($string) {
    return htmlspecialchars(strip_tags(trim($string)));
  }

  function concat($array1, $array2) {
    $array3 = array();

    foreach ($array1 as $element) {
      array_push($array3, $element);
    }

    foreach ($array2 as $element) {
      array_push($array3, $element);
    }

    return $array3;
  }

  function objectMap($callback, $array) {
    $mappedArray = array();

    foreach ($array as $key => $value) {
      $mappedArray[] = $callback($key, $value);
    }

    return $mappedArray;
  }

  function consoleLog($data) {
    echo "<script>console.log(" . json_encode($data) . ")</script>";
  }

  function throwError($message) {
    http_response_code(500);
    die($message);
    exit();
  }

  function sendErrorPage($error) {
    session_start();
    unset($_SESSION["error"]);

    $_SESSION["error"] = $error;
    $_SESSION["error"]["url"] = CURRENT_URL;

    header("Location: " . RELATIONAL_BASE_URL . "error.php");
    exit();
  }

  function listDirectory($directory) {
    $results = array_filter(glob("$directory/*"), "is_dir");

    $dirs = array();

    foreach ($results as $result) {
      $dir = str_replace("$directory/", "", $result);

      $folders = listDirectory($result);

      if (count($folders) > 0) {
        array_push($dirs, array("name" => $dir, "sub" => $folders));
      } else {
        array_push($dirs, $dir);
      }
    }

    return $dirs;
  }

  function listFile($directory) {
    $results = array_filter(glob("$directory/*"), "is_file");

    $files = array();

    foreach ($results as $result) {
      array_push($files, str_replace("$directory/", "", $result));
    }

    return $files;
  }

  function getURLParentLocation() {
    $locations = explode("/", $_SERVER["PHP_SELF"]);
    return $locations[count($locations) - 2];
  }

  function generateRedirectURL($url) {
    if (count($_GET) > 0) {
      $url = $url . (strpos($url, "?") !== false ? "&" : "?") . join("&", objectMap(function ($key, $value) {
        if (is_array($value)) {
          return join("&", array_map(function ($v) use ($key) { return $key . "[]=$v"; }, $value));
        } else {
          return "$key=$value";
        }
      }, $_GET));
    }

    return $url;
  }

  function generateRedirectButton($url, $buttonLabel) {
    if (count($_POST) > 0) {
      if (count($_GET) > 0) {
        $url = $url . (strpos($url, "?") !== false ? "&" : "?") . join("&", objectMap(function ($key, $value) {
          if (is_array($value)) {
            return join("&", array_map(function ($v) use ($key) { return $key . "[]=$v"; }, $value));
          } else {
            return "$key=$value";
          }
        }, $_GET));
      }

      $method = "post";
      $parameters = objectMap(function ($key, $value) {
        if (is_array($value)) {
          return join(array_map(function ($v) use ($key) {
            return "<input type=\"hidden\" name=\"$key" . "[]\" value=\"$v\" />";
          }, $value));
        } else {
          return "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
        }
      }, $_POST);
    } else {
      $method = "get";
      $parameters = objectMap(function ($key, $value) {
        if (is_array($value)) {
          return join(array_map(function ($v) use ($key) {
            return "<input type=\"hidden\" name=\"$key" . "[]\" value=\"$v\" />";
          }, $value));
        } else {
          return "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
        }
      }, $_GET);
    }

    return "
      <form action=\"$url\" method=\"$method\">
        " . join($parameters) . "
        <button type=\"submit\">$buttonLabel</button>
      </form>
    ";
  }
?>
