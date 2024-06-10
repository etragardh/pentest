<?php
/**
 * GOTMLS SESSION Start
 * @package GOTMLS
*/

if (!defined("GOTMLS_SESSION_TIME")) {
	define("GOTMLS_SESSION_TIME", microtime(true));

	function GOTMLS_session_start($ID_sess = false) {
		if (!session_id())
			session_start();
	if (!session_id()) {
		if ($ID_sess === true)
			$ID_sess = session_create_id();
		$GLOBALS["GOTMLS"]["tmp"]["previous_session_id"] = session_id();
		if ($GLOBALS["GOTMLS"]["tmp"]["previous_session_id"] && $ID_sess && ($ID_sess !== $GLOBALS["GOTMLS"]["tmp"]["previous_session_id"])) {
			session_write_close();
			session_id($ID_sess);
			session_start();
		} elseif ($GLOBALS["GOTMLS"]["tmp"]["previous_session_id"]) {
			if ($ID_sess === false)
				$ID_sess = $GLOBALS["GOTMLS"]["tmp"]["previous_session_id"];
			$GLOBALS["GOTMLS"]["tmp"]["previous_session_id"] = false;
		} else {
			if ($ID_sess)
				session_id($ID_sess);
			session_start();
		}
	}
		if (isset($_SESSION["GOTMLS_SESSION_TIME"]))
			$_SESSION["GOTMLS_SESSION_LAST"] = $_SESSION["GOTMLS_SESSION_TIME"];
		else
			$_SESSION["GOTMLS_SESSION_LAST"] = 0;
		$_SESSION["GOTMLS_SESSION_TIME"] = GOTMLS_SESSION_TIME;
		return $ID_sess;
	}

	function GOTMLS_session_close() {
		if ($GLOBALS["GOTMLS"]["tmp"]["previous_session_id"]) {
			if (session_id())
				session_write_close();
			session_id($GLOBALS["GOTMLS"]["tmp"]["previous_session_id"]);
			session_start();
		}
	}

	function GOTMLS_session_die($output, $header = "Content-type: text/javascript") {
		if ($header)
			@header($header);
		GOTMLS_session_close();
		die($output);
	}
}
