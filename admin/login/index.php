<?php include_once "process.php"; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once ADMIN_PATH . "includes/php/head.php"; ?>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="page-wrapper">
      <div class="page">
        <div class="headline">Administrator Login</div>
        <form method="post" class="login-form">
          <input type="text" name="username" placeholder="username" value="<?php echo $username; ?>" required autofocus />
          <input type="password" name="password" placeholder="password" value="<?php echo $password; ?>" required />
          <div class="error-message"><?php echo $errorMessage; ?></div>
          <button type="submit">login</button>
        </form>
      </div>
    </div>
  </body>
</html>
