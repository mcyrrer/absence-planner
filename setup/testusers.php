<?php
session_start();



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
echo "<a href='".$_SERVER['PHP_SELF']."?user=sifManager'>user sifManager</a><br>";
echo "<a href='".$_SERVER['PHP_SELF']."?user=sif1'>user sif1</a><br>";
echo "<a href='".$_SERVER['PHP_SELF']."?user=sif2'>user sif2</a><br>";
echo "<a href='".$_SERVER['PHP_SELF']."?user=sif3'>user sif3</a><br>";