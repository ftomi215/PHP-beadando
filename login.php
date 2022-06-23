<?php

include('storage.php');
include('auth.php');
include('userstorage.php');

// functions
function validate($post, &$data, &$errors) {
    if (!isset($post['username'])) {
        $errors['username'] = 'Username is not set';
      }
      else if (trim($post['username']) === '') {
        $errors['username'] = 'Username is required';
      }
      else {
        $data['username'] = $post['username'];
      }

      if (!isset($post['password'])) {
        $errors['password'] = 'Password is not set';
    }
    else if (trim($post['password']) === '') {
        $errors['password'] = 'Password is required';
    }
    else{
        $data['password'] = $post['password'];
    } 

    

  return count($errors) === 0;
}

function redirect($page) {
    header("Location: ${page}");
    exit();
}

// main
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if (count($_POST)>0) {
  if (validate($_POST, $data, $errors)) {
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
      $errors['global'] = "Incorrect username or password!";
    } else {
      $auth->login($auth_user);
      redirect('index.php');
    }
  }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($errors['username'])) : ?>
  <p><span class="error"><?= $errors['username'] ?></span></p>
<?php endif; ?>
<?php if (isset($errors['password'])) : ?>
  <p><span class="error"><?= $errors['password'] ?></span></p>
<?php endif; ?>
    <?php if (isset($errors['global'])) : ?>
  <p><span class="error"><?= $errors['global'] ?></span></p>
<?php endif; ?>
<form action="" method="post">
  <div>
    <label for="username">Username: </label><br>
    <input type="text" name="username" id="username">
    
  </div>
  <div>
    <label for="password">Password: </label><br>
    <input type="password" name="password" id="password">
    
  </div>
  <div>
    <button type="submit">Login</button>
  </div>
</form>

</body>
</html>