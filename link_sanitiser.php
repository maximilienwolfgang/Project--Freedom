<?php
define("ABSOLUTE_PATH", 1);
define("RELATIVE_PATH", 2);
define("ABSOLUTE_URL", 3);

class Link_Sanitiser {
	private function identify_link_type ($link) {
		if (strpos($link, "/") === 0 &&
			strpos($link, "/", 1) !== 0) {
			return ABSOLUTE_PATH;
		} else if (strpos($link, "http") === 0) {
			return ABSOLUTE_URL;
		} else {
			return RELATIVE_PATH;
		}
	}
	
	private function convert_link_to_url ($link) {
		$link_type = $this->identify_link_type($link);
		if ($link_type === ABSOLUTE_PATH) {
			return SITE_URL . $link;
		} else if ($link_type === RELATIVE_PATH) {
			return PAGE_URL.$link;
		} else {
			return $link;
		}
	}
	
	private function encrypt ($link) {
		return base64_encode($link);
	}
	
	private function add_proxy ($link) {
		return PROXY_URL . "?encurl=" . $link;
	}
	
	private function set_up_link ($link) {
		return $this->convert_link_to_url($link);
	}
	
	public function sort_hyperlinks ($html) {
		$links = array();
		// match:
		preg_match_all("/href=[^ \>]+[\"' \>\t]/", $html, $links);
		$links = $links[0];
		$links = preg_replace("/href=[\"']?/", "", $links);
		$links = preg_replace("/[]\"\>' \t]*/", "", $links);
		$linksassoc = array();
		foreach ($links as $link) {
			$linksassoc[$link] = $link;
		}
		$links = $linksassoc;
		unset($linksassoc);
		$i = 0;
		foreach ($links as $link) {
			$link = $this->set_up_link($link);
		}
		var_dump($links);
	}
}

echo PAGE_URL."\n";
echo SITE_URL."\n";