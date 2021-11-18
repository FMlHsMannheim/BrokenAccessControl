<?php
	function get_current_route() {
		$path = strtoupper(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$pathElements = explode('/', $path);
		$route = end($pathElements);
		if (array_key_exists($route, NAV_REF)) {
			return $route;
		}
		return 'LOGIN';
	}
	
	function get_user_from_route() {
		$path = strtoupper(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
		$pathElements = explode('/', $path);
		$usrIdStr = $pathElements[sizeof($pathElements)-2]; // second to last element
		
		if (is_numeric($usrIdStr))
		{
			return intval($usrIdStr);
		}
		return null;
	}
	
	define('CURRENT_ROUTE', get_current_route());
	
	define('USER_ROUTE_ID', get_user_from_route());

	?>