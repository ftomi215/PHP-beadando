<?php
include_once('storage.php');
include_once('teamstorage.php');
include_once('matchstorage.php');
include_once('userstorage.php');
include_once('auth.php');

$teamStorage = new TeamStorage();
$matchStorage = new MatchStorage();

session_start();
$auth = new Auth(new UserStorage());

$teams = $teamStorage->findAll([]);
$matches = $matchStorage->findAll([]);

$dates = array();



foreach ( $matches as $match):
    $date = new DateTime($match['date']);
    $now = new DateTime();
    $now->modify('+36 minutes');
    $now->modify('+8 seconds');
    if($date < $now){
        array_push($dates , $match["date"]);
    }
endforeach
;
function date_sort($a, $b) {
    return strtotime($b) - strtotime($a);
}
usort($dates, "date_sort");
$dates = array_slice($dates, 0, 5, true);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kezdőlap</title>
</head>
<body>

    <?php if (!$auth->is_authenticated()) {?>
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
    <?php } 
    else{?>
        <p>Hello, <?= $auth -> authenticated_user()['username'] ?>!</p>
        <a href="logout.php">Logout</a>
    <?php } ?>
    
    <table>
        <tr>
            <th>Csapatok</th>
        </tr>
        <?php foreach($teams as $team): ?>
        <tr>
            <td>
            <a href="team.php?id=<?= $team['id'] ?>">
            <?= $team['name'] ?>
          </a>
            </td>
        </tr>
        <?php endforeach ?>
    </table>

    
    
    <p>Meccsek:</p>
    <ul>
        <?php foreach ($dates as $date): ?>
        <?php foreach ($matches as $match) : 
                if($match["date"]==$date){
                    $date = new DateTime($match['date']);
                    $now = new DateTime();
                    $now->modify('+36 minutes');
                    $now->modify('+8 seconds');
    
                    if($date < $now) {?> <li><?= $teamStorage->findById($match['homeid'])["name"] ?> <?= $match["homescore"] ?> : <?= $match["awayscore"] ?> <?= $teamStorage->findById($match['awayid'])["name"] ?></li><?php }
                    else{?>
                    <li><?= $teamStorage->findById($match['homeid'])["name"] ?> : <?= $teamStorage->findById($match['awayid'])["name"] ?> még nem került lejátszásra</li>
                    <?php }
                }?>
               
                

            <?php endforeach ?>
            <?php endforeach ?>
    </ul>
</body>
</html>