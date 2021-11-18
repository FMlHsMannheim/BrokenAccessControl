<?php

session_unset();


function login_keys_exist($array) {
	return array_key_exists('email', $array)
		&& array_key_exists('password', $array);
}

function validate_login() {
	$usr = $_POST['email'];
	$pw = $_POST['password'];
	
	$loginJson = file_get_contents('users/0/loginData.json');
	$loginData = json_decode($loginJson, true);

	foreach ($loginData as $userId => $userData)
	{
		if ($userData["usr"] == $usr)
		{
			$pwHash = sha1($pw);
			$pwHash = str_replace('-', '', $pwHash);
			$pwHash = strtoupper($pwHash);

			if ($pwHash == $userData["sha1"])
			{
				// Found user, save login to session
				$_SESSION["usrId"] = $userId;

				return true;
			}
		}
	}

	return false;
}

function try_register() {
	$usr = $_POST['email'];
	$pw = $_POST['password'];

	$loginJson = file_get_contents('users/0/loginData.json');
	$loginData = json_decode($loginJson, true);

	print_r($loginData);

	$userId = null;
	foreach ($loginData as $userId => $userData)
	{
		if ($userData["usr"] == $usr)
		{
			//User exists
			return false;
		}
	}

	$newId = strval(intval($userId) + 1);
	
	$pwHash = sha1($pw);
	$pwHash = str_replace('-', '', $pwHash);
	$pwHash = strtoupper($pwHash);

	$loginData[$newId] = array( //Prepending the zero to force string key
		"usr" => $usr,
		"sha1" => $pwHash);

	$loginJson = json_encode($loginData);

	file_put_contents('users/0/loginData.json', $loginJson);

	mkdir('users/'.$newId);

	// Registered user, save login to session
	$_SESSION["usrId"] = $newId;

	return true;
}

//Handle login and registration requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
	login_keys_exist($_POST)) {
	
	if (isset($_POST['register'])) {

		if (try_register()) {
			//Redirect to home after registration
			header('Location: http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/BrokenAccessControl/'.$_SESSION["usrId"].'/home');
			die();
		}
		
		$errorMessage = 'Registration failed!';

	} else {

		if (validate_login()) {
			//Redirect to home after login
			header('Location: http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/BrokenAccessControl/'.$_SESSION["usrId"].'/home');
			die();
		}
		
		$errorMessage = 'Login failed!';
	}
}


?>


<!DOCTYPE HTML>
<html>
	<head>
		<title>Login</title>
		<link rel="stylesheet" href="main.css">
	</head>
	<body class="sign-in-up">
		<?php if (isset($errorMessage)) { echo '<div>'.$errorMessage.'</div>'; } ?>
		<div id="page-wrapper" class="sign-in-wrapper">
			<div class="graphs">
				<div class="sign-in-form">
					<div class="sign-in-form-top">
						<p><span>Sign in</span></p>
					</div>
					<div class="signin">
						<form id="loginForm" method="post">
							<div class="log-input">
								<div class="log-input-left">
									<input type="text" class="user" name="email" placeholder="E-Mail" required />
								</div>
							</div>
							<div class="log-input">
								<div class="log-input-left">
									<input type="password" class="lock" name="password" placeholder="Password" required />
								</div>
							</div>
							<div>
								<input class="loginbutton" type="submit" value="Login">
								<input name="register" class="loginbutton" type="submit" value="Register">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<footer>
			<p>SSE Gruppe A01</p>
		</footer>
	</body>
</html>
