<?php
/**
 * @package UnstoopidHighlighter
 * @version 0.1
 */
/*
Plugin Name: Unstoopid Highlighter
Plugin URI: http://wordpress.org/extend/plugins/unstoopid-include/
Description: A simple, hassle free way to pre blocks. No weird interfaces, no extra features.
Author: Jason Martin
Version: 0.1
Author URI: http://thebigrede.net
*/
//$test_content = file_get_contents("./test.txt");
$settings = array(
	'keywords' => array(
			'dotimes','dolist','do','format','for','when','then','if',
			'with','else','end', 'setf','defun','defvar','defmacro','defparameter',
			'list','mapcar','car','cdr','c[ad]+r', 'nil', 'loop','in',
			'maphash', 'make-[a-z\-]+','lambda','fn'),
	'patterns' => array(
		'/(".*?")/'				=> '<span class="unstoopid-highlight-string">$1</span>',
		'/([\(\)]+)/' 				=> '<span class="unstoopid-highlight-paren">$1</span>',
		'/([\d]+)/' 				=> '<span class="unstoopid-highlight-number">$1</span>',
	)
);
function unstoopid_highlight_css() {
	return file_get_contents( dirname(__FILE__) . "/style.css");
}

function unstoopid_maybe_highlight($content) {
	global $settings;
	preg_match_all("/<pre highlight.*?>(.*?)<\/pre>/s", $content, $matches);
	for ( $i = 0; $i < count($matches[1]); $i++) {
		$m = $matches[1][$i];
		$om = $m;
		$m = str_replace(" ","&nbsp;",$m);
		$m = str_replace("\t","&nbsp;&nbsp;&nbsp;",$m);
		foreach( $settings['patterns'] as $pattern=>$replacement) {
			$m = preg_replace($pattern, $replacement, $m);
		}
		$m = str_replace("\n","<br />\n",$m);
		foreach ( $settings['keywords'] as $kword) {
			$m = preg_replace("/($kword)&nbsp;/", "<span class='unstoopid-highlight-keyword'>\$1 </span>", $m);
		}
		$m = preg_replace('/([\*]{1,4}[a-zA-Z\-]+[\*]{0,4})/','<span class="unstoopid-highlight-global">$1</span>',$m);
		$m = preg_replace('/(:[a-zA-Z\-]+)/','<span class="unstoopid-highlight-symbol">$1</span>',$m);
		$m = preg_replace('/(&nbsp;t&nbsp;)/','<span class="unstoopid-highlight-boolean">$1</span>',$m);
		$m = preg_replace('/(t&nbsp;)/','<span class="unstoopid-highlight-boolean">$1</span>',$m);
		$m = preg_replace("/(#')/",'<span class="unstoopid-highlight-sharpquote">$1</span>',$m);
		$content = str_replace($matches[0][$i], '<div class="unstoopid-highlight-div">' . $m . '</div>', $content);
	}
	$new_content = "<style>" . unstoopid_highlight_css() . "</style>";
	return $new_content . $content;
}
function unstoopid_highlight_init() {
	add_filter( 'the_content', 'unstoopid_maybe_highlight' );
}

add_action("init","unstoopid_highlight_init",10);
