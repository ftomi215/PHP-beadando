<?php
include_once('matchstorage.php');
include_once('teamstorage.php');
include_once('teamstorage.php');
include_once('userstorage.php');
include_once('auth.php');

session_start();
$auth = new Auth(new UserStorage());

$matchStorage = new MatchStorage();
$teamStorage = new TeamStorage();
$userStorage = new UserStorage();



$matchid = $_GET['id'];
$teamid = $_GET['team_id'];
$match = $matchStorage->findById($matchid);




function validate($post, &$data, &$errors, &$match) {

    $data['id']=$_GET['id'];
    $data['homeid']=$match['homeid'];
    $data['awayid']=$match['awayid'];

    if (!isset($post['homescore'])) {
        $errors['homescore'] = 'Score is not set';
      }
      else if (trim($post['homescore']) === '') {
        $errors['homescore'] = 'Score is required';
      }
      else if (!is_numeric($post['homescore'])) {
        $errors['homescore'] = 'Score is not numeric';
      }
      else {
        $data['homescore'] = $post['homescore'];
      }

      if (!isset($post['awayscore'])) {
        $errors['awayscore'] = 'Score is not set';
      }
      else if (trim($post['awayscore']) === '') {
        $errors['awayscore'] = 'Score is required';
      }
      else if (!is_numeric($post['awayscore'])) {
        $errors['awayscore'] = 'Score is not numeric';
      }
      else {
        $data['awayscore'] = $post['awayscore'];
      }

      if (!isset($post['date'])) {
        $errors['date'] = 'Date is not set';
      }
      else if (trim($post['date']) === '') {
        $errors['date'] = 'Date is required';
      }
      else if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $post['date'])){
        $errors['date']='Date is in wrong format';
    }
      else {
        $data['date'] = $post['date'];
      }
    

  return count($errors) === 0;
}




$errors = [];
$data = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors, $match)) {
    $matchStorage->update($matchid,$data);
    
    $site ="Location: team.php?id=" . $teamid;
    
    header($site);
    exit();
  }
}









?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modify - <?=$_GET['id']?></title>
</head>
<body>
  <h1>Edit match</h1>
  
  <form action="" method="post" novalidate>
  <?=$teamStorage->findById($match['homeid'])["name"]?>: 
  <input type="text" name="homescore" required
      value="<?= $_POST['homescore'] ?? $match['homescore'] ?>"
    > 
    <?php if(isset($errors['homescore'])) : ?>
      <span><?= $errors['homescore'] ?></span>
    <?php endif ?>
    <br>
    <?=$teamStorage->findById($match['awayid'])["name"]?>: 
    <input type="text" name="awayscore" required
      value="<?= $_POST['awayscore'] ?? $match['awayscore'] ?>"
    > 
    <?php if(isset($errors['awayscore'])) : ?>
      <span><?= $errors['awayscore'] ?></span>
    <?php endif ?>
    <br>
    Date: <input type="text" name="date" required
      value="<?= $_POST['date'] ?? $match['date'] ?>"
    > 
    <?php if(isset($errors['date'])) : ?>
      <span><?= $errors['date'] ?></span>
    <?php endif ?>

        <br>


    <a href="deletematch.php?id=<?=$matchid?>&team_id=<?=$teamid?>">Delete match</a>
    <br>

    
    <button>Save</button>
  </form>
</body>
</html>