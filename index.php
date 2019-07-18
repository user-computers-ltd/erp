<?php
  define("ROOT_PATH", "");

  include_once ROOT_PATH . "includes/php/config.php";
  include_once ROOT_PATH . "includes/php/utils.php";

  $systems = listDirectory("systems");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>ERP</title>
    <?php include_once ROOT_PATH . "includes/php/head.php"; ?>
    <style>
      body {
        background-color: #eeeeee;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cg fill-rule='evenodd'%3E%3Cg fill='%23000000' fill-opacity='0.05' %3E%3Cpath d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM3 17.9v12.7l10.99 6.34 11-6.35V17.9l-11-6.34L3 17.9zM0 15l12.98-7.5V0h-2v6.35L0 12.69v2.3zm0 18.5L12.98 41v8h-2v-6.85L0 35.81v-2.3zM15 0v7.5L27.99 15H28v-2.31h-.01L17 6.35V0h-2zm0 49v-8l12.99-7.5H28v2.31h-.01L17 42.15V49h-2z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      }

      .top-link {
        font-weight: bold;
      }

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
        <a href="<?php echo BASE_URL; ?>admin" class="top-link">Admin</a>
        <h2>Systems</h2>
          <div class="system-list">
          <?php if (count($systems) > 0) : ?>
            <?php
              foreach ($systems as $system) {
                $name = $system["name"];
                echo "<a href=\"" . BASE_URL . "systems/$name\" class=\"system-link\"><span>$name</span></a>";
              }
            ?>
          <?php else : ?>
            <div class="no-results">No systems built yet</div>
          <?php endif ?>
        </div>
      </div>
    </div>
  </body>
</html>
