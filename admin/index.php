<?php
  define("ROOT_PATH", "../");
  define("ADMIN_PATH", ROOT_PATH . "admin/");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ADMIN_PATH . "includes/php/utils.php";

  define("ADMIN_URL", BASE_URL . "admin/");

  $breadcrumbs = array(
    array("url" => BASE_URL, "label" => "Main")
  );
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once ADMIN_PATH . "includes/php/head.php"; ?>
    <style>
      .system-list {
        margin: 40px 0px;
        text-align: center;
      }

      .system-list .system-link {
        display: inline-block;
        position: relative;
        margin: 10px;
        width: 100px;
        height: 100px;
        color: #000000;
        border: 1px solid #000000;
        border-radius: 5px;
        transition: box-shadow 0.2s;
      }

      .system-list .system-link:hover {
        box-shadow: 0px 1px 2px #a0a0a0;
      }

      .system-list .system-link span {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
      }
    </style>
  </head>
  <body>
    <div class="page-wrapper">
      <div class="page">
        <?php include_once ADMIN_PATH . "includes/components/header.php"; ?>
        <h2>Administrator</h2>
        <div class="system-list">
          <a href="<?php echo ADMIN_URL . "databases"; ?>" class="system-link"><span>Databases</span></a>
          <a href="<?php echo ADMIN_URL . "systems"; ?>" class="system-link"><span>Systems</span></a>
        </div>
      </div>
    </div>
  </body>
</html>
