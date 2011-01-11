<pre>
<?php
define("BITLY_API_KEY",
	urlencode("R_3663116da9326b73d08a603e38a2bbeb"));
define("PROXY_URL",
	urlencode("http://sitesdir/freedom/index.php"));


if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (isset($_POST["url"])) {
		$url = $_POST["url"];
		$enc = FALSE;
	} else if (isset($_POST["encurl"])) {
		$url = $_POST["encurl"];
		$enc = TRUE;
	} else {
		die();
	}
} else if ($_SERVER["REQUEST_METHOD"] === "GET") {
	if (isset($_GET["url"])) {
		$url = $_GET["url"];
		$enc = FALSE;
	} else if (isset($_GET["encurl"])) {
		$url = $_GET["encurl"];
		$enc = TRUE;
	} else {
		die();
	}
} else {
	die();
}

if ($enc) {
	$url = base64_decode($url);
}


if (strpos($url, "www") === 0) {
	$url = "http://." . $url;
} else if (strpos($url, "http") !== 0) {
	$url = "http://www." . $url;
}

$deconstructed_url = parse_url($url);

$splurl = str_split($url);
if ($splurl[strlen($url) - 1] !== "/") {
	$url .= "/";
}

$page_contents = file_get_contents($url);

define("SITE_URL", $deconstructed_url["scheme"] .
					"://" .
					$deconstructed_url["host"]);
define("PAGE_URL", $url);

include "link_sanitiser.php";
include "word_replace.php";

$link_sanitiser = new Link_Sanitiser;
$word_replacer = new Word_Replace;

$html = file_get_contents(PAGE_URL);
// $html = $word_replacer->replace_html($html);
$html = $link_sanitiser->sort_hyperlinks($html);
// echo $html;