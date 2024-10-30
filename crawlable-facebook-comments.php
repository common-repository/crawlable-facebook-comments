<?php
/*
Plugin Name: Crawlable Facebook Comments
Plugin URI: http://www.zewlak.com/
Description: Let Google crawl and index your Facebook Comments. Crawlable Facebook Comments plugin retrieves comments (with authors, dates and replies) and makes them visible in your source code.
Version: 0.2
Author: Tomasz Zewlakow
Author URI: http://www.zewlak.com/
*/


add_action( 'wp_print_styles', 'enqueue_my_styles' );

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'fbCrawl_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'fbCrawl_remove' );

add_shortcode("crawlable-facebook-comments", "shortcode_handler");

function shortcode_handler() {

  //run function that actually does the work of the plugin
  $shortcode_output = load_comments();
  //send back text to replace shortcode in post
  return $shortcode_output;
}

function enqueue_my_styles(){
	wp_enqueue_style('fbCrawlStyle',plugins_url( 'css/fbCrawlStyle.css' , __FILE__ ));
}

function fbCrawl_install() {
/* Creates new database field */
add_option("fbCrawl_date", 'l jS \of F Y h:i:s', '', 'yes');
add_option("fbCrawl_debug", 'false', '', 'yes');
}

function fbCrawl_remove() {
/* Deletes the database field */
delete_option('fbCrawl_date');
delete_option('fbCrawl_debug');
}

function load_comments()
{

if(eregi("Googlebot",$_SERVER['HTTP_USER_AGENT'])){
$ip = $_SERVER['REMOTE_ADDR'];
$name = gethostbyaddr($ip);
$host = gethostbyname($name);

if(eregi("Googlebot",$name)){


if ($host == $ip){

/* ------------ start printing comments -------------- */

	// displays some comments for a certain url
	$url = get_permalink();
	
	// fql multiquery to fetch all the data we need to display in one go
	$queries = array('q1' => 'select post_fbid, fromid, object_id, text, time from comment where object_id in (select comments_fbid from link_stat where url ="'.$url.'")',
					 'q2' => 'select post_fbid, fromid, object_id, text, time from comment where object_id in (select post_fbid from #q1)',
					 'q3' => 'select name, id, url, pic_square from profile where id in (select fromid from #q1) or id in (select fromid from #q2)',
					 );

	// note format json-strings is necessary because 32-bit php sucks at decoding 64-bit ints :(
	$result = json_decode(file_get_contents('http://api.facebook.com/restserver.php?format=json-strings&method=fql.multiquery&queries='.urlencode(json_encode($queries))));

	$comments = $result[0]->fql_result_set;
	$replies = $result[1]->fql_result_set;
	$profiles = $result[2]->fql_result_set;
	$profiles_by_id = array();
	foreach ($profiles as $profile) {
	  $profiles_by_id[$profile->id] = $profile;
	}
	$replies_by_target = array();
	foreach ($replies as $reply) {
	  $replies_by_target[$reply->object_id][] = $reply;
	}

	/**
	 * print a comment and author, given a comment passed in an an array of all profiles.
	 * @param object $comment as returned by q1 or q2 of the above fql queries
	 * @param array $profiles_by_id, a list of profiles returned by q3, keyed by profile id
	 * @returns string markup
	 */
	function pr_comment($comment, $profiles_by_id) {
	  $profile = $profiles_by_id[$comment->fromid];
	  $author_markup = '';
	  if ($profile) {
		$author_markup =
		  '<span class="profile">'.
			'<img class="photo" src="'.$profile->pic_square.'" align=left />'.
			'<a class="name" href="'.$profile->url.'" target="_blank">'.$profile->name.'</a>'.
		  '</span>';
	  }
		$dateFormat = get_option('fbCrawl_date');
	  return
		$author_markup.
		'<div class="date">&nbsp;'.date('l jS \of F Y h:i:s A',$comment->time).'</div>'.
		''.
		'<p>'.$comment->text.'</p>';
	}
	echo '<div id="comments">';
	// print each comment
	foreach ($comments as $comment) {
	  print
		'<div class="fbcomments">'.
		  pr_comment($comment, $profiles_by_id).
		'</div>';
	  // print each reply
	  if (!empty($replies_by_target[$comment->post_fbid])) {
		foreach ($replies_by_target[$comment->post_fbid] as $reply) {
		  print
			'<div class="fbcomments_reply">'.
			  pr_comment($reply, $profiles_by_id).
			'</div>';
		}
	  }
	}
	echo '</div>';
/* ------------ end printing comments -------------- */

}

}

}




}


if ( is_admin() ){

	/* Call the html code */
	add_action('admin_menu', 'fbCrawl_admin_menu');

	function fbCrawl_admin_menu() {
		add_options_page('Crawlable Facebook Comments Configuration', 'Crawlable FB Comments', 'administrator',
		'fbCrawl', 'fbCrawl_html_page');
	}
}
?>
<?php
function fbCrawl_html_page() {
?>
<div class="wrap"><div id="icon-tools" class="icon32"></div><h2>Crawlable Facebook Comments Configuration</h2>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="widefat">
<tr valign="top">



</tr>

</table>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="fbCrawl_debug" />

<!--<p>
<input class="button-primary" type="submit" value="<?php _e('Save Changes') ?>" />
</p>-->

</form>

<table class="widefat">
<thead>
    <tr>
        <th>Usage</th>
    </tr>
</thead>
<tbody>
   <tr><td>As we all know deafult facebook <fb:comments></fb:comments> displays comments iframe so they are not visible to Googlebot. Make your comments search engine optimized and valuable in SEO! Let Google crawl and index your Facebook Comments. Every time Googlebot visites your site Crawlable Facebook Comments plugin retrieves comments (with authors, dates and replies) and makes them visible in your source code.</td></tr>

   <tr>
     	<td>Instalation:<br/>Use shortcode <strong>[crawlable-facebook-comments]</strong> in every page you use Facebook Comments<br/>
	Or paste the code in your template file: <b>if(function_exists('load_comments')) { load_comments(); } <br/><span style="font-size:16px;">You shouldn't see a difference on your site unless you are Googlebot!</span></b></td>
   </tr>
   <tr>
	<td>This plugin doesn't allow your visitors to comment posts etc.<br/>You need some other plugin for that, e.g. <a href="http://wordpress.org/extend/plugins/facebook-comments-plugin/">Facebook Comments</a><br/>
	Example of usage and trial here: <a href="http://www.zewlak.com/crawlable-facebook-comments/">Wordpress</a>
   </tr>
</tbody>
</table>

<table class="widefat">
<thead>
    <tr>
        <th>Donation</th>
	<th></th>
    </tr>
</thead>
<tbody>
   <tr>
     	<td>If you find this plugin useful be so kind to consider a donation:<br/>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post"><span style="font-family: Arial;"><span style="line-height: normal;"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=WQXR3DDCWLHFL&amp;lc=PL&amp;item_name=Crawlable%20Facebook%20Comments&amp;currency_code=USD&amp;bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted"><img class="alignnone" title="Donate" src="http://www.zewlak.com/paypal.gif" alt="" width="160" height="47" /></a></span></span></form>

</td>
   </tr>
</tbody>
</table>
</div>
<?php
}
?>