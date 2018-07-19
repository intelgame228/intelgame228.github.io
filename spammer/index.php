<?php
	require_once("../php/classPanel.php");
	require_once("../php/classSpammerPanelPageGenerator.php");
	
	session_start();
	
	$url_array = parse_url($_SERVER['REQUEST_URI']);
	$path = $url_array["path"];
	
	$Panel = new classPanel();
	
	if(!empty($_POST)){
		if (isset($_POST["auth"])) {
			if ($Panel->b_CheckOnValidSpammerAcc($_POST["email"], $_POST["password"])) {
				$_SESSION["spammer_login"] = $_POST["email"];
				$_SESSION["spammer_password"] = $_POST["password"];
			}
			else {
				header("Location: /spammer/auth");
			}
		}
	}
	
	if (!empty($_GET)){
		if (array_key_exists("do", $_GET)) {
			if (($_GET["do"] == "SetPaymentSystem") && isset($_GET["id"]) && isset($_GET["payment_system"])) {
				$Panel->void_SetPaymentSystem($_GET["id"], $_GET["payment_system"]);
			}
		}
	}
	
	if (
		((!isset($_SESSION["spammer_login"]) || !isset($_SESSION["spammer_password"]))) && 
		(!($Panel->b_checkAdmin(@$_SESSION["spammer_login"], @$_SESSION["spammer_password"]))) && 
		(($path !== "/spammer/auth") && ($path !== "/spammer/auth/"))
	) header("Location: /spammer/auth");
	
	$PanelPageGen = new PageGenerator("template");
	
	switch ($path){
		case "/spammer/auth": 
		case "/spammer/auth/": 
			if (isset($_SESSION["spammer_login"]) && isset($_SESSION["spammer_password"])){
				header("Location: /spammer");
			}
			else echo $PanelPageGen->GenerateAuth();
		break;
		
		case "/spammer/logout": 
		case "/spammer/logout/": 
			if (isset($_SESSION["spammer_login"]) && isset($_SESSION["spammer_password"])){
				unset($_SESSION["spammer_login"]);
				unset($_SESSION["spammer_password"]);
			}
			header("Location: /spammer/auth");
		break;
		
		case "/spammer": 
		case "/spammer/": 
			echo $PanelPageGen->GenerateIndex();
		break;
		
		case "/spammer/newaccs": 
		case "/spammer/newaccs/": 
			if ($Panel->b_newAccs($_SESSION["spammer_login"])) echo "1";
			$Panel->void_setNotificated();
		break;
	}
?>