<!-- <nav class="navbar sticky-top navbar-light" style="background-color: #e3f2fd;">
  <div class="container">
    <a href="index.php" class="navbar-brand">Ying Admin</a>
    <ul class="navbar-nav mr-auto mt-2 mt-md-0">
      <li><a href="brands.php">Brands</a></li>
      <li><a href="categories.php">categories</a></li>
      <li class="dropdown">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
         <li><a href="#"></a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav> -->

<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="index.php">Ying Admin</a>

  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <ul class="navbar-nav mr-auto mt-2 mt-md-0">
      <li class="nav-item active">
        <a class="nav-link" href="brands.php">Brands <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="categories.php">Categories <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="products.php">Products <span class="sr-only">(current)</span></a>
      </li>
      <?php  if(has_permission('admin')): ?>
      <li class="nav-item active">
        <a class="nav-link" href="users.php">Users <span class="sr-only">(current)</span></a>
      </li>
    <?php endif; ?>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Hello <?=$user_data['first'];?>!
        <span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
          <li><a href="change_password.php">Change Password</a></li>
          <li><a href="logout.php">logout</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
