<?PHP
//
// Dumb Terminal Tor Exit List Generator
// DT-TorIPsList.php
// php file to echo list of Tor exit nodes for a particular ip/dns
//// http://check.torproject.org/cgi-bin/TorBulkExitList.py?ip=<ip>
//   execute via: 
//     php cli  ( php DT-TorIPsList.php <ip|dns> <WebViewOn|WebViewOff|on|off> )
//     apache php  ( /DT-TorIPsList.php?WebServer=<ip|dns>&WebView=<on|off> )
//   Default IP/DNS priority (highest to lowest): 
//     php CLI [see above]
//     apache php [see above]
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
//// 2011-04-09 19:35:05 PST
// 
// TODO:
//   option to append DNS and/or IP to Post+Pre List Text  area
//   ability to concatenate lists and sort+unique the lists of multiple WebServers
$VER="0.9.1";


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

$NL="";
if(substr($_SERVER['PATH'],0,1) == '/'){
  //is linux
  $NL="\n";
}else{
  //is windows
  $NL="\r\n";
}


if (!empty($argv[2]) )
  if ( strtolower($argv[2]) == "webviewon"  ||  strtolower($argv[2]) == "on" )
    $useInBrowser=true;
  elseif ( strtolower($argv[2]) == "webviewoff"  ||  strtolower($argv[2]) == "off" )
    $useInBrowser=false;

$getWEBVIEW=@$_GET["WebView"];
if (!empty($getWEBVIEW))
  if( strtolower($getWEBVIEW) == "on" )
    $useInBrowser=true;
  if( strtolower($getWEBVIEW) == "off" )
    $useInBrowser=false;


$IP=$WebServer;

$getSRV=@$_GET["WebServer"];
if (!empty($getSRV))
  $IP = $getSRV;

if ( !empty($argv[1]) ) 
  $IP = $argv[1];

if ( !validateIP( $IP) ) {
  $ADDRinput=$IP;
  $IP=gethostbyname($IP);

  if ( !validateIP( $IP ) ) {
    echo "invalid IP or DNS: \"$ADDRinput\"";
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
  echo "<!DOCTYPE html> ".$NL."<html id=\"home\" lang=\"eng\"> ".$NL."<head> ".$NL."<title>TorIPsList</title> ".$NL."<body> ".$NL;
  $outs_append=$outs_append." <br />";
}


echo "$preLISTtext".$NL;

if( $useInBrowser ) {  echo "<br />".$NL;  }

for ($i=3; $i <= sizeof($curlOUTput)-1; $i++ ) {
  echo $preIPtext."$curlOUTput[$i]".$postIPtext.$outs_append.$NL;
}

echo "$postLISTtext".$NL;



if( $useInBrowser ) {
  echo $NL."</body>".$NL."</html>";
}


?>