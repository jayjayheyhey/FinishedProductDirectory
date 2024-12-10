<?php
if(isset($_GET['error']) && $_GET['error'] == 1) {
    $errorMessage = "Not Found, Incorrect Email or Password.";

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterfil Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container" id="logIn">
        <h1 class="form-title">Log In</h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
          <div class="input-group">
              <i class="fas fa-user"></i>
              <input type="text" name="username" id="username" placeholder="Username" required>
              <label for="username">Username</label>
          </div>
          <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Password" required>
              <label for="password">Password</label>
          </div>
         <input type="submit" class="btn" value="Log In" name="signIn">
        </form>
    </div>
      <script src="script.js"></script>
</body>
</html>