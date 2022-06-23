<?php
include_once('matchstorage.php');
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



$matchStorage->delete($matchid);

$site ="Location: team.php?id=" . $teamid;
    
header($site);
exit();



