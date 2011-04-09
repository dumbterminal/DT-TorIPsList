<?PHP
//
// Dumb Terminal Tor Exit List Generator
// DT-TorIPsList.php
// php file to echo list of Tor exit nodes for a particular ip/dns
//// http://check.torproject.org/cgi-bin/TorBulkExitList.py?ip=<ip>
//   execute via: 
//     php cli (php DT-TorIPsList.php <ip|dns>)
//     apache php (with ability to export with html code)
//   Default IP/DNS priority (highest to lowest): 
//     first parameter from CLI  ( php DT-TorIPsList.php <ip|dns> )
//     DT-TorIPsList.php?WebServer=<ip|dns>
//     php script variable: WebServer
//   ability to echo to stdout, or HTML5
//   allows appending text to begenning and/or end of the Entire List and Each Line
//   script has minimal error checking
// uses PHP exec and requires the program 'curl'
// Default output format for generating .htaccess file deny list
// 
// PHP file by veekahn
// planned script implemtations: BASH, perl, python, VBS 
//
// http://dt.tehspork.com
// Dumb Terminal
//  Smaller than Life Projects
// Main Page: http://dt.tehspork.com
// Git Repo: https://github.com/dumbterminal/
// By: MikereDD & veekahn
// email: dumbterminal -at- tehspork.com
// 
//// Last Update
//// 2011-04-08 21:12:42 PST
// 
// TODO:
//   option to append DNS and/or IP to Post+Pre List Text  area
$VER="0.9";


// Editable section for configuration


// ip or dns allowed
$WebServer="google.com";


$useInBrowser=false;
$useInBrowser=true;

$preLISTtext="#Tor Script List";
$postLISTtext="#end Tor Script List";

$preIPtext="deny from ";
$postIPtext="";







//// stay the hell out of the area below this line ////
///////////////////////////////////////////////////////

function validateIP($ip) 
{ 
  $regexp = "([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})"; 
  //  $validate = ereg($regexp, $ip); 
  $validate = preg_match($regexp, $ip); 
  if ($validate == true) 
    return true; 
  else 
    return false; 
} 


$IP=$WebServer;

$getSRV=@$_GET["WebServer"];
if (!empty($getSRV))
  $IP = $getSRV;

if ( !empty($argv[1]) ) 
  $IP = $argv[1];

if ( !validateIP( $IP) ) {
  $IP=gethostbyname($IP);

  if ( !validateIP( $IP ) ) {
    echo "invalid IP: \"$WebServer\"";
    exit;
  }
}
$curlCMD="curl http://check.torproject.org/cgi-bin/TorBulkExitList.py?ip=".$IP;

@exec (  $curlCMD, $curlOUTput, $curlRTN );

if ( $curlRTN ) {
  echo "attempted to execute curl and failed (php exec 'curl' return value of $curlRTN).";
  exit;
}

$outs_append="";
if( $useInBrowser ) {
  echo "<!DOCTYPE html> \n<html id=\"home\" lang=\"eng\"> \n<head> \n<title>TorIPsList</title> \n<body> \n";
  $outs_append=$outs_append." <br />";
}


echo "$preLISTtext\n";

if( $useInBrowser ) {  echo "<br />\n";  }

for ($i=3; $i <= sizeof($curlOUTput)-1; $i++ ) {
  echo $preIPtext."$curlOUTput[$i]".$postIPtext.$outs_append."\n";
}

echo "$postLISTtext\n";



if( $useInBrowser ) {
  echo "\n</body>\n</html>";
}


?>