<?php
require_once '../classes/autoloader.php';
require_once '../settings.inc';

session_start();

echo '<a href="../index.php">Back to index</a><br>';
$dbm = new DbHelper();
$con = $dbm->connectToMainDb();

if(isset($_REQUEST['user']))
{
    session_destroy();
    session_start();
    $_SESSION['user']=$_REQUEST['user'];
    if(strcmp("sifManager",$_REQUEST['user'])==0)
    {
        $_SESSION['manager']=true;
        $_SESSION['managerlevel'] =3;
    }
}

if(isset($_SESSION['user']))
{
    echo "You are ".$_SESSION['user']."<br><br>";
    echo "Session data:<br>".print_r($_SESSION,1)."<br><br>";
}

echo "Who do you want to be?<br>";

$sql = "SELECT u.username as username, u.fullname as fullname, u.team as team ,u.manager as manager ,m.manager_user_id  as m_id FROM users u LEFT JOIN mangers m ON u.username = m.manager_user_id;";
$result = mysqli_query($con,$sql);
echo "[username][fullname][managerid]<br>";
while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
{
    echo "<a href='".$_SERVER['PHP_SELF']."?user=".$row['username']."'>[".$row['username'] ."]-[".$row['fullname'] ."] [".$row['m_id']."]</a><br><br>";

}
