<?php

//Functions
function get_home_link($userId) {
	return 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/BrokenAccessControl/'.$userId.'/home';
}

function get_file_location($filename) {
    return 'users/'.USER_ROUTE_ID.'/'.$filename;
}

//Access control
$loginLink = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'/BrokenAccessControl/login';

// Block access if not logged in
if (!array_key_exists('usrId', $_SESSION)) {
	header('Location: '.$loginLink);
    die();
}

//Block accessing the subdir of other users or non-existant files
if (!defined('USER_ROUTE_ID') ||
    !($_SESSION['usrId'] == USER_ROUTE_ID ||
	$_SESSION['usrId'] == "0") ||
    !array_key_exists('file', $_REQUEST)) {
    
		header('Location: '.get_home_link($_SESSION["usrId"]));
		die();
}


$filename = $_REQUEST['file'];
$filename = get_file_location($filename);
$filename = realpath($filename);

$usersDir = realpath('users');

$fileDir = pathinfo($filename, PATHINFO_DIRNAME);


if (substr($fileDir, 0, strlen($usersDir)) != $usersDir) {
    //Trying to break out of users folder
    header('Location: '.get_home_link($_SESSION["usrId"]));
    die();
}

if (!file_exists($filename)) {
    header('Location: '.get_home_link($_SESSION["usrId"]));
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" &&
    array_key_exists("content", $_POST)) {
    //Save request

    file_put_contents($filename, $_POST["content"]);
}


?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Filemanager</title>
        <link rel="stylesheet" href="../main.css">
    </head>
    <body>
        <a href="<?php echo get_home_link($_SESSION["usrId"])?>">Back</a>
        <form id="fileForm" method="post">
            <textarea name="content" class="datei_textarea" rows="15" cols="80"><?php echo file_get_contents($filename); ?></textarea>
            <div>
                <input type="submit" value="Save">
            </div>
        </form>
        <div id="errorDiv"></div>
    </body>
    <script defer>
        function onlySendValid(event) {
            if (<?php
                echo (null == (substr($fileDir, 0, strlen($usersDir.DIRECTORY_SEPARATOR.USER_ROUTE_ID)) != $usersDir.DIRECTORY_SEPARATOR.USER_ROUTE_ID) ? 'false' : 'true');
                //Client side validation: file is in current user subfolder
                //This check should be implemented on serverside, redirecting to the home directory, if it does not return true
                ?>) {
                
                let errorDiv = document.getElementById('errorDiv');
                errorDiv.innerText = 'You do not have permission to edit this file!';
                event.preventDefault();
            }
        }

        let form = document.getElementById('fileForm');

        form.addEventListener('submit', (e) => onlySendValid(e));
    </script>
</html>
