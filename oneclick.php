<?php
//
// Edit the next three values to fit your implementation
//
$statusfile = '/usr/local/nagios/var/status.dat';
$commandfile = '/usr/local/nagios/var/rw/nagios.cmd';
$htpassfile = '/var/httpd/conf/htpass';
// Stop editing

ini_set("memory_limit","256M");
require_once './include/http_authenticate.php';

if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])){
    // this simply means that they have submitted the login form for this realm
    $auth=http_authenticate($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'],$htpassfile,'SHA');
    define('USER_AUTHENTICATED',$auth);
}

if(defined('USER_AUTHENTICATED') && USER_AUTHENTICATED){
    // authentication successful

    echo '<?xml version="1.0" encoding="iso-8859-1"?>',"\n";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>One-Click Acknowledge</title>
<style>
h3 {
  color: #EF4223;
  font: bold 15px arial;
}
</style>
</head>
<body>
<h3>Nagios Critical Acknowledgements</h3>
<pre>
<?php
$input = file_get_contents($statusfile);
$pattern = '/([a-z]+) {(.*)}/msU';
preg_match_all($pattern, $input, $matches);
$filter = "2";
$score = 0;

foreach ( $matches[2] as $value ) {
        $pattern = '/([a-z_]+)\=(.*)/';
        if ( $_GET['debug'] == 1 ) { print_r($params[0]); }
        preg_match_all($pattern, $value, $params, PREG_PATTERN_ORDER);
        if ( $params[1][1] == "service_description" && $params[2][14] > 1 && $params[2][46] == "0" && $params[2][42] == "1" ) {
                $now = exec("date +%s");
                $cmd = '/usr/bin/printf "[%lu] ACKNOWLEDGE_SVC_PROBLEM;'.$params[2][0].';'.$params[2][1].';1;0;1;'.$_SERVER['PHP_AUTH_USER'].';Script Ack\n" '.$now.' > '.$commandfile;
                exec($cmd);
                echo $params[2][0].": ".$params[2][1]."\n";
                $score++;
        }
        elseif ( $params[1][0] == "host_name" && $params[1][1] != "service_description" && $params[2][13] > "0" && $params[2][41] == "0" && $params[2][40] == "1" ) {
                $now = exec("date +%s");
                $cmd = '/usr/bin/printf "[%lu] ACKNOWLEDGE_HOST_PROBLEM;'.$params[2][0].';1;0;1;'.$_SERVER['PHP_AUTH_USER'].';Script Ack\n" '.$now.' > '.$commandfile;
                exec($cmd);
                echo $params[2][0]."\n";
                $score++;
        }
}

if ( $score > 0 ) {
        echo "Acknowledged $score alert(s).";
} else {
        echo "No results.";
}

?>
</pre>
</body>
</html>
<?php
} else{
    // The user has not been authenticated, present a login form.
    header('WWW-Authenticate: Basic realm="SiteSpect HTTP Auth"');
    header('HTTP/1.0 401 Unauthorized');

    // If the user cancels the login form, below is what they get.
    exit('Authentication is required to view this page.');
}
?>
