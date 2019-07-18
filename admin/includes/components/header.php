<div>
  <form action="<?php echo ADMIN_LOGOUT_URL; ?>" class="logout-form">
    <button>log out</button>
  </form>
  <?php if (assigned($breadcrumbs) && count($breadcrumbs) > 0) : ?>
    <?php foreach ($breadcrumbs as $index => &$breadcrumb) : ?>
      <?php if ($index > 0) : ?>
        <span>></span>
      <?php endif ?>
      <a href="<?php echo $breadcrumb["url"]; ?>" class="breadcrumb">
        <?php echo $breadcrumb["label"]; ?>
      </a>
    <?php endforeach ?>
  <?php endif ?>
</div>
