<?php $mizenkauserId=$_SESSION['mizenkauserId'];
echo "test";
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 900)) {
    // last request was more than 30 minates ago (1800)
   // session_destroy();   // destroy session data in storage
    unset($_SESSION['mizenkauserId']);
	unset($_SESSION['mizenkauserName']);
	unset($_SESSION['mizenkafirstName']);
	unset($_SESSION['mizenkalastName']);
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp to session
$_SESSION['timeoutval'] = 1;
$_SESSION['cartlogin'] = 1;
echo $_SESSION['timeoutval'];
echo "dfsf".$_SESSION['cartlogin'];
die();

if(!isset($_SESSION['mizenkauserId'])) {
	if(isset($_COOKIE['mizenkaLastvisit']))	{
		$_SESSION['mizenkauserId']			=	$_COOKIE['mizenkaLastvisit'];
		$_SESSION['mizenkauserName']		=	$_COOKIE['mizenkaLastvisitname'];
		$_SESSION['mizenkafirstName']		=	$_COOKIE['mizenkaLastvisitfirstName'];
		$_SESSION['mizenkalastName']		=	$_COOKIE['mizenkaLastvisitlastName'];
	}else{		
		//header("location:../login.php"); exit();
	}
	
}
?>