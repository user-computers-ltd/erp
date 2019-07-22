<?php include_once "process.php"; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once ADMIN_PATH . "includes/php/head.php"; ?>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <?php include_once ROOT_PATH . "includes/components/confirm-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/prompt-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/message-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/import-csv-table-dialog.php"; ?>
    <?php include_once ROOT_PATH . "includes/components/request-loader.php"; ?>
    <div class="page-wrapper">
      <div class="page landscape">
        <?php include_once ADMIN_PATH . "includes/components/header.php"; ?>
        <h2><?php echo $table; ?></h2>
        <?php if ($databaseFound && $tableFound) : ?>
          <div class="table-tab">
            <div class="tab active" onclick="openTab(event, 'content')">
              content
            </div><div class="tab" onclick="openTab(event, 'structure')">
              structure
            </div>
          </div>
          <div class="tab-content table-content show">
            <form class="table-query" method="post">
              <textarea name="sql"><?php echo $sql; ?></textarea>
              <button type="submit">execute statement</button>
              <div class="table-buttons">
                <?php if ($isSystemTable) : ?>
                  <div class="image-button restart-image tooltip" onclick="restartTable('<?php echo $table; ?>', true)">
                    <span class="tooltip-text">Restart `<?php echo $table; ?>` from system settings</span>
                  </div>
                <?php endif ?>
                <div class="image-button clear-import-image tooltip" onclick="clearImportTable('<?php echo $table; ?>', <?php echo $columnNames; ?>)">
                  <span class="tooltip-text">Remove data & import CSV to `<?php echo $table; ?>`</span>
                </div>
                <div class="image-button import-image tooltip" onclick="importTable('<?php echo $table; ?>', <?php echo $columnNames; ?>)">
                  <span class="tooltip-text">Import CSV to `<?php echo $table; ?>`</span>
                </div>
                <div class="image-button export-image tooltip" onclick="exportTable('<?php echo $table; ?>')">
                  <span class="tooltip-text">Export CSV from `<?php echo $table; ?>`</span>
                </div>
                <div class="image-button copy-image tooltip" onclick="copyTable('<?php echo $table; ?>')">
                  <span class="tooltip-text">Copy `<?php echo $table; ?>`</span>
                </div>
                <div class="image-button clear-image tooltip" onclick="clearTable('<?php echo $table; ?>')">
                  <span class="tooltip-text">Remove data from `<?php echo $table; ?>`</span>
                </div>
                <div class="image-button delete-image tooltip" onclick="deleteTable('<?php echo $table; ?>')">
                  <span class="tooltip-text">Delete `<?php echo $table; ?>`</span>
                </div>
              </div>
            </form>
            <?php if ($resultCount > 0) : ?>
              <div class="table-query-result-header">
                <div class="table-query-result-count">
                  Total <?php echo "$resultCount row" . ($resultCount > 1 ? "s" : ""); ?>
                </div>
                <form class="table-query-result-settings" method="post">
                  <textarea name="sql" hidden><?php echo $sql; ?></textarea>
                  <?php
                    for ($i = 0; $i < $pageCount; $i++) {
                      $index = $i + 1;
                      if ($pageNo === $i) {
                        echo "<span>$index</span>";
                      } else if ($i === 0 || $i === ($pageCount - 1) || ($pageNo !== $i && abs($pageNo - $i) < 4)) {
                        $offsetValue = $i * $count;
                        echo "<button type=\"submit\" name=\"offset\" value=\"$offsetValue\">$index</button>";
                      } else if (abs($pageNo - $i) === 4) {
                        echo "...";
                      }
                    }
                  ?>
                  <select name="count" onchange="this.form.submit()">
                    <option value="100" <?php echo $count === 100 ? "selected" : ""; ?>>100</option>
                    <option value="200" <?php echo $count === 200 ? "selected" : ""; ?>>200</option>
                    <option value="500" <?php echo $count === 500 ? "selected" : ""; ?>>500</option>
                  </select>
                </form>
                <button class="table-query-result-export" onclick="exportResults()">export results</button>
              </div>
              <div class="table-query-results-wrapper">
                <table class="table-query-results gridline sticky-head">
                  <thead>
                    <tr>
                      <?php foreach ($results[0] as $key => $value) : ?>
                        <th><?php echo $key; ?></th>
                      <?php endforeach; ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      for ($i = $offset; $i < count($results) && $i < $offset + $count; $i++) {
                        echo "<tr>" . implode(array_map(function ($v) { return "<td>$v</td>"; }, $results[$i])) . "</tr>";
                      }
                    ?>
                  </tbody>
                </table>
              </div>
            <?php elseif (assigned($error)) : ?>
              <div class="table-query-error"><?php echo $error; ?></div>
            <?php else : ?>
              <div class="no-results">No results</div>
            <?php endif ?>
          </div>
          <div class="tab-content table-structure">
            <?php if (count($columns) > 0) : ?>
              <div class="table-structure-results-wrapper">
                <table class="table-structure-results gridline sticky-head">
                  <colgroup>
                    <?php foreach ($columns[0] as $key => $value) : ?>
                      <col>
                    <?php endforeach ?>
                    <col style="width: 30px">
                  </colgroup>
                  <thead>
                    <tr>
                      <?php foreach ($columns[0] as $key => $value) : ?>
                        <th><?php echo $key; ?></th>
                      <?php endforeach ?>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($columns as &$column) : ?>
                      <tr>
                        <?php echo implode(array_map(function ($c) { return "<td>$c</td>"; }, $column)); ?>
                        <td>
                          <div class="remove" onclick="deleteColumn('<?php echo $column["field"]; ?>')">&times;</div>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>
            <?php else : ?>
              <div class="no-results">No columns</div>
            <?php endif ?>
          </div>
          <script>
            var database = "<?php echo $database; ?>";
            var table = "<?php echo $table; ?>";
            var sql = "<?php echo preg_replace("/\"/", "\\\"", $sql); ?>";
            var apiURL = "<?php echo ADMIN_URL; ?>includes/php/api.php";
          </script>
          <script src="script.js"></script>
        <?php else : ?>
          <div class="no-results">Database or table not found</div>
        <?php endif ?>
      </div>
    </div>
  </body>
</html>
