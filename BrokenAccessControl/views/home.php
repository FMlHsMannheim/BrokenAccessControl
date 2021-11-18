<?php

// Read user data
$loginJson = file_get_contents('users/0/loginData.json');
$loginData = json_decode($loginJson, true);

//Functions
function get_file_link($filename) {
	return 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/BrokenAccessControl/'.USER_ROUTE_ID.'/edit?file='.$filename;
}

function get_home_link($userId) {
	return 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/BrokenAccessControl/'.$userId.'/home';
}

function try_create_file($newFilename) {
	global $errorMessage;

	//Relative path protection
	if (!preg_match('~^[\p{L}\p{N}]+$~uD', $newFilename)) {
		
		$errorMessage = 'Only letters and digits are allowed!';
		return;
	}

	$newFilename = 'users/'.USER_ROUTE_ID.'/'.$newFilename.'.txt';
	
	if (file_exists($newFilename)) {
		$errorMessage = 'File already exists!';
		return;
	}

	$newFile = fopen($newFilename, 'w');
	fclose($newFile);
}

function try_delete_file($filename) {
	global $errorMessage;

	//Relative path protection. Also only allows handling of txt files
	if (!preg_match('~^[\p{L}\p{N}]+\.txt$~uD', $filename)) {
		return 400; //Bad request
	}

	$filename = 'users/'.USER_ROUTE_ID.'/'.$filename;

	$absolutePath = realpath($filename);


	if (!file_exists($absolutePath)) {
		return 404; //Not Found
	}

	if (unlink($absolutePath)) {
		//Disclosure of directory structure and server information!
		//This should not be returned, the status code is sufficient!
		print("Successfully deleted file: ");
		print_r($absolutePath);
		return 200; //OK
	}

	return 500; //Internal server error
}

//Access control
$loginLink = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/BrokenAccessControl/login';
if (!defined('USER_ROUTE_ID') || !array_key_exists(USER_ROUTE_ID, $loginData)) {
	//Accessing home without specifying userId -> redirect to own home or login
	//Accessing home of non-existant user -> redirect to own home or login

	if (array_key_exists('usrId', $_SESSION)) {
		header('Location: '.get_home_link($_SESSION["usrId"]));
		die();
	}
	header('Location: '.$loginLink);
	die();
}

//Violation of deny by default. Missing the case of unregistered users ('usrId' does not exist in $_SESSION)
//Fix: default to redirect; only show content if userId matches or is 0
if (array_key_exists('usrId', $_SESSION) &&
	$_SESSION['usrId'] != USER_ROUTE_ID &&
	$_SESSION['usrId'] != "0") {
	//Accessing home of other user as non-admin-user -> redirect to own home

	header('Location: '.get_home_link($_SESSION["usrId"]));
	die();
}



//Load user files
$userFiles = new DirectoryIterator('users/'.USER_ROUTE_ID);

//Handle create file requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
	array_key_exists('filename', $_POST)) {
	
	$filename = $_POST["filename"];

	if (array_key_exists('delete', $_POST) &&
		$_POST['delete']) {

		$responseCode = try_delete_file($filename);
		http_response_code($responseCode);
		die();

	} else {
		try_create_file($filename);
	}
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>
		Home
	</title>
	<script defer>
		function deleteFile(filename) {
			const delRequest = new XMLHttpRequest();
			delRequest.onload = function () {
				switch (this.status) {
					case 200:
						document.getElementById(filename).remove();
						break;
					case 400:
						alert('Invalid file name!');
						break;
					case 404:
						alert('The file does not exist!');
						break;
					case 500:
						alert('Internal server error!');
					default:
						alert('Unexpected error!');
						break;
				}
			}

			delRequest.open("POST", "<?php echo get_home_link(USER_ROUTE_ID) ?>", false);

			delRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

			delRequest.send('filename=' + filename + '&delete=true');
		}
	</script>
</head>
<body>
	<header>
		<h1>Home</h1>
	</header>
	<main>
		<div>
			<a href="<?php echo $loginLink ?>">Logout</a>
		</div>
		<h2>Documents</h2>
		<div>
			<div>
				<table>
				<?php foreach ($userFiles as $file) {
					if (!$file->isDot()) {
						$filename = $file->getFilename();
						echo
							'<tr id="'.$filename.'">
								<td>
									<a href="'.get_file_link($filename).'">'.pathinfo($filename, PATHINFO_FILENAME).'</a>
								</td>
								<td>
									<button onclick="deleteFile(\''.$filename.'\')">Delete</button>
								</td>
							</tr>';
					}
				} ?>
				</table>
			</div>
			<br/>
			<div>
				<form method="post">
					<div>
						<label>File name:  
							<input type="text" name="filename" placeholder="Required" required>
						</label>
						<input type="submit" value="Create file" margin="10,0">
					</div>
					<?php if (isset($errorMessage)) { echo '<div>'.$errorMessage.'</div>'; } ?>
				</form>
			</div>
		</div>
	</main>
</body>