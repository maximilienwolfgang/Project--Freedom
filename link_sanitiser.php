<?php
define("ABSOLUTE_PATH", 1);
define("RELATIVE_PATH", 2);
define("ABSOLUTE_URL", 3);
define("ANCHOR", 4);

/**
 * Link_Sanitiser
 * 
 * Link Sanitiser works with an html document and converts all href
 * attributes to the proxy equivalents.
 * 
 * PHP version 5
 * 
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without
 * modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in the
 * documentation and/or other materials provided with the distribution.
 * + Neither the name of the <ORGANIZATION> nor the names of its
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category  Link_Sanitiser
 * @package   Link_Sanitiser
 * @author    Max Bucknell-Leahy <max.bucknell@gmail.com>
 * @copyright 2011 Max Bucknell-Leahy
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * The BSD License
 * @version   0.1
 * @link      http://www.max.dafmusic.com/tag/project-freedom
 * @link	  http://www.github.com/maxbucknell/Project--Freedom
 */
class Link_Sanitiser {

    /**
     * Finds out what type of link a link is
     * 
     * @param  string $link the link
     * @return integer sentinel indicating link type
     * @access private
     */
	private function identify_link_type ($link) {
		// all absolute links start with "/", but not "//", because
		// a "//" represents a protocol relative url
		if (strpos($link, "/") === 0 &&
			strpos($link, "/", 1) !== 0) {
			return ABSOLUTE_PATH;
		} else if (strpos($link, "http") === 0 ||
			strpos($link, "//") === 0) {
			return ABSOLUTE_URL;
		// not gonna mess with internal anchors. Maybe if I encounter
		// a bug where there is a blocked word as an id, but not yet
		} else if (strpos($link, "#") === 0) {
			return ANCHOR;
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
		} else if ($link_type === ANCHOR) {
			return NULL;
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
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $link Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access private
     */
	private function set_up_link ($link) {
		return $this->convert_link_to_url($link);
	}
	
    /**
     * Prepares an html document for proxying
     * 
     * It's a multi-step thing. First it searches the document for
	 * any match of href, and stores the text until it hits a " " or
	 * ">". This ensures that we get the whole link.
	 *
	 * Next, I clean it up by taking out all the cruft, like quotes,
	 * or hrefs, whitespaces etc.
	 *
	 * I then run each one through the link sorter outer thing. It
	 * encodes the url, then adds it as a get param to the proxy url.
     * 
     * @param  unknown $html Parameter description (if any) ...
     * @return void   
     * @access public 
     */
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
			if ($link === NULL) {
				unset($link);
			}
		}
		var_dump($links);
	}
}

$link = new Link_Sanitiser;
$link->sort_hyperlinks(file_get_contents("index.html"));