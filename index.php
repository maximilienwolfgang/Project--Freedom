<?php
define("PROXY_URL",
	"http://sitesdir/Project--Freedom/index.php");


// if ($_SERVER["REQUEST_METHOD"] === "POST") {
// 	if (isset($_POST["url"])) {
// 		$url = $_POST["url"];
// 		$enc = FALSE;
// 	} else if (isset($_POST["encurl"])) {
// 		$url = $_POST["encurl"];
// 		$enc = TRUE;
// 	} else {
// 		die();
// 	}
// } else if ($_SERVER["REQUEST_METHOD"] === "GET") {
// 	if (isset($_GET["url"])) {
// 		$url = $_GET["url"];
// 		$enc = FALSE;
// 	} else if (isset($_GET["encurl"])) {
// 		$url = $_GET["encurl"];
// 		$enc = TRUE;
// 	} else {
// 		die();
// 	}
// } else {
// 	die();
// }

// if ($enc) {
// 	$url = base64_decode($url);
// }
// 
// 
// if (strpos($url, "www") === 0) {
// 	$url = "http://." . $url;
// } else if (strpos($url, "http") !== 0) {
// 	$url = "http://www." . $url;
// }
// 
// $deconstructed_url = parse_url($url);
// 
// $splurl = str_split($url);
// 
// define("SITE_URL", $deconstructed_url["scheme"] .
// 					"://" .
// 					$deconstructed_url["host"]);
// define("PAGE_URL", $url);
define("SITE_URL", "http://sitesdir");
define("PAGE_URL", "http://sitesdir/Project--Freedom");

include "link_sanitiser.php";
include "word_replace.php";

$link_sanitiser = new Link_Sanitiser;
$word_replacer = new Word_Replace;

$html = file_get_contents("index.html");
$html = $word_replacer->replace_html($html);
$html = $link_sanitiser->sort_hyperlinks($html);
echo $html;