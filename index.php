<?php

	session_start();

	require_once('config.php');
	require_once('routing.php');
	
	require_once(CONTENT_REF[CURRENT_ROUTE]);

	?>