<?php
include_once('commentstorage.php');
include_once('teamstorage.php');
include_once('userstorage.php');
include_once('auth.php');

session_start();
$auth = new Auth(new UserStorage());

$commentStorage = new CommentStorage();
$teamStorage = new TeamStorage();
$userStorage = new UserStorage();

$commentid = $_GET['id'];
$teamid = $_GET['team_id'];



$commentStorage->delete($commentid);

$site ="Location: team.php?id=" . $teamid;
    
header($site);
exit();



