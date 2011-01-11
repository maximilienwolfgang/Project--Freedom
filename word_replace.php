<?php
/**
 * Handles the changing of words for Project: Freedom
 * 
 * The phrases to be replaced are in a file called
 * "word_replace_config.php" in a comma delimeted key, value format.
 * It is used as a method of circumventing the impero system which
 * detects words or phrases in a web page and censors it if one shows
 * up. This is works by filtering words and replacing them with
 * something more innocuous.
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
 * @category  Project_Freedom
 * @package   Project_Freedom
 * @author    Max Bucknell-Leahy <max.bucknell@gmail.com>
 * @copyright 2011 Max Bucknell-Leahy
 * @license   http://www.opensource.org/licenses/bsd-license.php 
 * The BSD License
 * @version   1.0
 * @link      http://max.dafmusic.com/tag/project-freedom
 *
 * @todo Create some nice interface for modifying the config file.
 */
class Word_Replace {

    /**
     * Associative array of the phrases to censor.
     * @var    array  
     * @access private
     */
	private $_words;

	public function __construct () {
		$config = file("word_replace_config.php",
			FILE_IGNORE_NEW_LINES);
		$this->_words = $this->_process($config);
	}
	
    /**
     * Reads the config file into an array
     * 
     * It takes an array given by the file() function, and it splits
	 * each item of that into a key and value, making it easier to
	 * work with.
     * 
     * @param  array   $old_words result of file() of config file
     * @return array   Associative array based on $old_words
     * @access private
     */
	private function _process ($old_words) {
		$new_words = array();
		foreach ($old_words as $word) {
			$word = str_split($word);
			$word_length = count($word);
			$i = 0;
			$k = "";
			$v = "";
			$find = "k";
			
			while ($i < $word_length) {
				if ($i < $word_length - 1 &&
					$word[$i].$word[$i + 1] === "\\,") {
					$$find .= $word[$i + 1];
					$i += 2;
				} else if ($word[$i] === ",") {
					$find = "v";
					$i += 1;
				} else {
					$$find .= $word[$i];
					$i += 1;
				}
			}
			$new_words[trim($k)] = trim($v);
		}
		return $new_words;
	}
	
    /**
     * Replaces the phrases marked for replacement from html
     * 
     * 
     * @param  unknown $html Parameter description (if any) ...
     * @return array   Return description (if any) ...
     * @access public 
     */
	public function replace_html ($html) {
		// where we are going to store the text from the htm;
		// document (that is, not html tags)
		$text = array();
		
		// we have to split the associative array into two separate
		// arrays to work with the replacement functions
		$words_to_replace = array();
		$words_replacement = array();
		
		// when we have filtered the text, we need to store it in a
		// new array, because we need to keep the old stuff.
		$new_text = array();
		
		// populate the arrays defined above. Convert the words to
		// replace into regular expressions.
		foreach ($this->_words as $word => $replacement) {
			$words_to_replace[] = "/".$word."/i";
			$words_replacement[] = $replacement;
		}
		
		// everything between '>' and '<'.
		// i.e. everything that isn't inside an html tag
		preg_match_all("/\>[^\<]+\</", $html, $text);
		
		// temporary array to get rid of the empty array items, since
		// not all html tags have text inside
		$text_rep = array();
		
		// preg_match stores things in a multi dimensional array.
		// since we don't need that functionality, we just get rid
		// of it.
		$text = $text[0];
		
		foreach ($text as $string) {
			// remove the wrapping remnants of html
			$string = trim($string, "><");
			// and any whitespace
			$string = trim($string);
			
			// then we don't add any empty items to the new array
			if ($string !== "") {
				$text_rep[] = $string;
			}
		}
		
		// we're all finished now
		$text = $text_rep;
		unset($text_rep);
		
		//filter the words and store it in the array we made up top
		$new_text = preg_replace($words_to_replace,
			$words_replacement,
			$text);
			
		// and that's all folks
		 return str_replace($text, $new_text, $html);
	}
}