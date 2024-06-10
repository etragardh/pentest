<?php
/**
 * GOTMLS wp-login protection
 * @package GOTMLS
*/

if (!defined("GOTMLS_LOGIN_PROTECTION"))
	define("GOTMLS_LOGIN_PROTECTION", microtime(true));
if (!defined("GOTMLS_REQUEST_METHOD"))
	define("GOTMLS_REQUEST_METHOD", (isset($_SERVER["REQUEST_METHOD"])?strtoupper($_SERVER["REQUEST_METHOD"]):"none"));
if (!(isset($GLOBALS["GOTMLS"]) && is_array($GLOBALS["GOTMLS"])))
	$GLOBALS["GOTMLS"] = array();
if (!isset($GLOBALS["GOTMLS"]["detected_attacks"]))
	$GLOBALS["GOTMLS"]["detected_attacks"] = '';
if (is_file(dirname(__FILE__)."/session.php")) {
	include(dirname(__FILE__)."/session.php");
	if (!function_exists("GOTMLS_update_log_file")) {
		function GOTMLS_update_log_file($dont_force_write = true) {
			if (!defined("GOTMLS_SESSION_FILE"))
				define("GOTMLS_SESSION_FILE", dirname(__FILE__)."/_SESSION/index.php");
			if (is_file(GOTMLS_SESSION_FILE))
				include(GOTMLS_SESSION_FILE);
			else {
				if (!is_dir(dirname(GOTMLS_SESSION_FILE)))
					@mkdir(dirname(GOTMLS_SESSION_FILE));
				if (is_dir(dirname(GOTMLS_SESSION_FILE)))
					if (!is_file(GOTMLS_SESSION_FILE))
						if (file_put_contents(GOTMLS_SESSION_FILE, "<?php if (!defined('GOTMLS_INSTALL_TIME')) define('GOTMLS_INSTALL_TIME', '".GOTMLS_SESSION_TIME."');"))
							include(GOTMLS_SESSION_FILE);
			}
			if (!defined("GOTMLS_INSTALL_TIME"))
				return false;
			else {
				$GOTMLS_LOGIN_ARRAY = array("ADDR"=>(isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"REMOTE_ADDR"), "AGENT"=>(isset($_SERVER["HTTP_USER_AGENT"])?$_SERVER["HTTP_USER_AGENT"]:"HTTP_USER_AGENT"), "TIME"=>GOTMLS_INSTALL_TIME);
				$GOTMLS_LOGIN_KEY = md5(serialize($GOTMLS_LOGIN_ARRAY));
				if (!defined("GOTMLS_LOG_FILE"))
					define("GOTMLS_LOG_FILE", dirname(GOTMLS_SESSION_FILE)."/GOTMLS.$GOTMLS_LOGIN_KEY.php");
				if (is_file(GOTMLS_LOG_FILE))
					include(GOTMLS_LOG_FILE);
				if (GOTMLS_REQUEST_METHOD == "POST")
					$GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY][GOTMLS_REQUEST_METHOD][GOTMLS_INSTALL_TIME] = $GOTMLS_LOGIN_ARRAY;
				else
					$GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY][GOTMLS_REQUEST_METHOD] = GOTMLS_INSTALL_TIME;
				@file_put_contents(GOTMLS_LOG_FILE, '<?php $GLOBALS["GOTMLS"]["logins"]["'.$GOTMLS_LOGIN_KEY.'"]=unserialize(base64_decode("'.base64_encode(serialize($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY])).'"));');
				if (isset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]) && is_array($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]))
					return $GOTMLS_LOGIN_KEY;
				else
					return 0;
			}
		}
	}

	if (isset($_GET["GOTMLS_sess"]) && strlen($_GET["GOTMLS_sess"]) && isset($_GET["GOTMLS_time"]) && is_numeric($_GET["GOTMLS_time"])) {
		define("GOTMLS_SESS", preg_replace('/[^0-9\-,a-z]/i', "", $_GET["GOTMLS_sess"]));
		define("GOTMLS_TIME", preg_replace('/[^0-9]/', "", $_GET["GOTMLS_time"]));
		GOTMLS_session_start(GOTMLS_SESS);
	} elseif (isset($_POST["GOTMLS_sess_id"]) && strlen($GOT_sess = preg_replace('/[^0-9\-,a-z]/i', "", $_POST["GOTMLS_sess_id"]))) {
		define("GOTMLS_SESS", "$GOT_sess");
		GOTMLS_session_start("$GOT_sess");
	} else
		define("GOTMLS_SESS", GOTMLS_session_start(true));
	if ((GOTMLS_REQUEST_METHOD == "POST") && isset($_POST["log"]) && isset($_POST["pwd"]) && !(isset($GOTMLS_LOGIN_KEY) && isset($GOTMLS_logins[$GOTMLS_LOGIN_KEY]["whitelist"]))) {
		if (!(isset($_SESSION["GOTMLS_detected_attacks"]) && $_SESSION["GOTMLS_SESSION_LAST"]))
			$GLOBALS["GOTMLS"]["detected_attacks"] = '&attack[]=NO_SESSION';
		elseif (isset($_POST["GOTMLS_sess_id"]) && strlen($GOT_sess) && isset($_POST["GOTMLS_sess_$GOT_sess"]) && is_numeric($_POST["GOTMLS_sess_$GOT_sess"])) {
			if (isset($_SESSION["GOTMLS_server_time"]["sess_$GOT_sess"]["JS_time"]) && isset($_SESSION["GOTMLS_server_time"]["sess_$GOT_sess"]["PHP_time"])) {
	//			$diff = ($_SESSION["GOTMLS_server_time"]["sess_$GOT_sess"]["PHP_time"] * 1000) - $_SESSION["GOTMLS_server_time"]["sess_$GOT_sess"]["JS_time"];
//echo "<li>$diff</li>\n";
				$diff = substr(preg_replace('/[^1-9]/', "", md5($_SESSION["GOTMLS_server_time"]["sess_$GOT_sess"]["PHP_time"])).'111111111111', 0 , 12);
				$JS_time = round(($_POST["GOTMLS_sess_$GOT_sess"] - $diff) / 60000);
				$PHP_time = round((time() - $_SESSION["GOTMLS_server_time"]["sess_$GOT_sess"]["PHP_time"]) / 60);
				if ((($PHP_time - $JS_time) > 2) || (($JS_time - $PHP_time) > 2))
					$GLOBALS["GOTMLS"]["detected_attacks"] = '&attack[]=WRONG_JS';
//die("<li>$diff</li>\n<li>$JS_time</li>\n<li>$PHP_time</li>\n".json_encode(array($_POST["GOTMLS_sess_$GOT_sess"]."~".time()."<Li>"=>$_SESSION["GOTMLS_server_time"]["sess_$GOT_sess"])));
			} else
				$GLOBALS["GOTMLS"]["detected_attacks"] = '&attack[]=NO_JS';
		}
		if (!isset($_SERVER["REMOTE_ADDR"]))
			$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_REMOTE_ADDR';
		if (!isset($_SERVER["HTTP_USER_AGENT"]))
			$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_HTTP_USER_AGENT';
		if (!isset($_SERVER["HTTP_REFERER"]) && !(isset($_SERVER["HTTP_USER_AGENT"]) && substr($_SERVER["HTTP_USER_AGENT"], 0, 18) == "Mozilla/5.0 (iPad;"))
			$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_HTTP_REFERER';
		if (!(isset($GLOBALS["GOTMLS"]["detected_attacks"]) && $GLOBALS["GOTMLS"]["detected_attacks"])) {
			if (isset($_SESSION["GOTMLS_login_attempts"]) && is_numeric($_SESSION["GOTMLS_login_attempts"]) && strlen($_SESSION["GOTMLS_login_attempts"]."") > 0)
				$_SESSION["GOTMLS_login_attempts"]++;
			else {
				if ($GOTMLS_LOGIN_KEY = GOTMLS_update_log_file()) {
					if (!(isset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"]) && is_array($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"])))
						$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_LOGIN_ATTEMPTS';
					elseif (!isset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["GET"]))
						$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_LOGIN_GETS';
					else {
						$_SESSION["GOTMLS_login_attempts"] = 0;
						foreach ($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"] as $LOGIN_TIME=>$LOGIN_ARRAY) {
							if ($LOGIN_TIME > $GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["GET"])
								$_SESSION["GOTMLS_login_attempts"]++;
							else
								unset($GLOBALS["GOTMLS"]["logins"][$GOTMLS_LOGIN_KEY]["POST"][$LOGIN_TIME]);
						}
					}
				} else
					$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_LOG_FILE';
			}
			if (!(isset($_SESSION["GOTMLS_login_attempts"]) && is_numeric($_SESSION["GOTMLS_login_attempts"]) && ($_SESSION["GOTMLS_login_attempts"] < 6) && $_SESSION["GOTMLS_login_attempts"]))
				$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=TOO_MANY_login_attempts';
		}
		if (isset($GLOBALS["GOTMLS"]["detected_attacks"]) && $GLOBALS["GOTMLS"]["detected_attacks"])
			require(dirname(__FILE__)."/index.php");
	} else {
		if (isset($_SERVER["SCRIPT_FILENAME"]) && basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]))
			GOTMLS_update_log_file();
		$_SESSION["GOTMLS_detected_attacks"] = '';
		$_SESSION["GOTMLS_login_attempts"] = 0;
	}
	//GOTMLS_session_close();
} else {
	$GLOBALS["GOTMLS"]["detected_attacks"] .= '&attack[]=NO_session.php_FILE';
	require(dirname(__FILE__)."/index.php");
}
