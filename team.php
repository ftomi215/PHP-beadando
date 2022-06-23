<?php
include_once('storage.php');
include_once('teamstorage.php');
include_once('matchstorage.php');
include_once('commentstorage.php');
include_once('userstorage.php');
include_once('auth.php');

session_start();
$auth = new Auth(new UserStorage());

$isadmin=false;

if ($auth->is_authenticated()){
    if (in_array("admin", $auth->authenticated_user()['roles'])){
        $isadmin=true;
    }
}




$teamStorage = new TeamStorage();
$matchStorage = new MatchStorage();
$commentStorage = new CommentStorage();
$userStorage = new UserStorage();


$team = $teamStorage->findById($_GET['id']);
$matches = $matchStorage->findAll([]);
$comments = $commentStorage->findAll([
    'teamid' => $team['id'],
]);
$users = $userStorage->findAll([]);

function validate($post, &$data, &$errors) {
    $data = $post;
    // Ellenőrzés
    if (!isset($post['text'])) {
      $errors['text'] = 'Üres a komment mező!';
    }
    else if (trim($post['text']) === '') {
      $errors['text'] = 'Üres a komment mező!';
    }
    else {
      $data['text'] = $post['text'];
    }
  

  
    return count($errors) === 0;
  }

$data = [];
$errors = [];
if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        
        $data['author']=$auth->authenticated_user()['id'];
        $data['teamid']=$_GET['id'];
        date_default_timezone_set("Europe/Budapest");
        $data['time']=date('Y-m-d H:i:s', time()+ 2168);
        $commentStorage->add($data);
        header('Location: team.php?id='.$_GET['id']); // POST -> GET
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
    <title><?= $team['name'] ?> - csapatleírás</title>
</head>

<body>
    <a href="index.php">Vissza a kezdőlapra</a>

    <p><?= $team['name'] ?></p>

    <table>
        <tr>
            <td>Csapatnév</td>
            <td><?= $team['name'] ?></td>
        </tr>
        <tr>
            <td>Város</td>
            <td><?= $team['city'] ?></td>
        </tr>
    </table>

    <p>Meccsek:</p>
    <ul style="list-style-type:none;">
        <?php foreach ($matches as $match) : ?>
            <?php if ($match["homeid"] == $team["id"] || $match["awayid"] == $team["id"]) { 
                $date = new DateTime($match['date']);
                $now = new DateTime();
                $now->modify('+36 minutes');
                $now->modify('+8 seconds');

                if ($match["homeid"] == $team["id"]){
                    if($match["homescore"]>$match["awayscore"]){
                        $color="Green";
                    }
                    elseif($match["homescore"]<$match["awayscore"]){
                        $color="Red";
                    }
                    elseif($match["homescore"]==$match["awayscore"]){
                        $color="Yellow";
                    }
                }
                else{
                    if($match["homescore"]<$match["awayscore"]){
                        $color="Green";
                    }
                    elseif($match["homescore"]>$match["awayscore"]){
                        $color="Red";
                    }
                    elseif($match["homescore"]==$match["awayscore"]){
                        $color="Yellow";
                    }
                }
                


                if($date < $now &&  $match["date"]!=="" && $match["homescore"]!=="" &&  $match["awayscore"]!=="") {?> 
                
                    <li style="color:<?=$color?>"><?= $teamStorage->findById($match['homeid'])["name"] ?> <?= $match["homescore"] ?> : <?= $match["awayscore"] ?> <?= $teamStorage->findById($match['awayid'])["name"] ?></li>
                   
                   
                
                
                <?php  }
                else{?>
                <li><?= $teamStorage->findById($match['homeid'])["name"] ?> : <?= $teamStorage->findById($match['awayid'])["name"] ?> még nem került lejátszásra</li>
                <?php }?>

                <?php if($isadmin){ ?>
                        <a href="modify.php?id=<?=$match['id']?>&team_id=<?=$team['id']?>">Modify&#x2934</a>
                <?php } ?>
            <?php } ?>
                

            <?php endforeach ?>
    </ul>

    <p>Kommentek:</p>
    <ul>
    <?php foreach ($comments as $comment) : ?>
        <li>
            <?= $userStorage->findOne([
                'id' => $comment['author']])['username']?><br>
            <?= $comment['text'] ?><br>
            <?= $comment['time'] ?>
        </li>
        <?php if($isadmin){ ?>
            <a href="deletecomment.php?id=<?=$comment['id']?>&team_id=<?=$team['id']?>">Delete&#x2934</a>
        <?php } ?>
    <?php endforeach ?>
    </ul>


    <p>Új komment írása:</p>

    <?php if ($auth->is_authenticated()) {?>
        <form action="" method="post" novalidate>
        <input type="text" name="text">
        <?php if(isset($errors['text'])): ?>
            <span style="color: red"> <?= $errors['text'] ?> </span>
        <?php endif ?>
        <br>
        <button>Küldés</button>
    </form>
    <?php } 
    else{?>
        <p>Hozzászólás írása csak bejelentkezés után lehetséges!</p>
        <a href="login.php">Login</a>
    <?php } ?>
    
    

        

</body>

</html>