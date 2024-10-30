<?php
/*
Plugin Name: BlogActivityShortcode
Plugin URI: 
Description: This plugin displays blogs activity forum like page when the user activaes this plugn and adds [blog_activity] code onto that page.
Version: 0.1
Author: OLT UBC
Author URI: http://blogs.ubc.ca/oltdev
*/
 
/*
== Installation ==
 
1. Upload BlogActivityShortcode.zip to the /wp-content/plugins/BlogActivityShortcode/BlogActivityShortcode.php directory
2. Unzip into its own folder /wp-content/plugins/
3. Activate the plugin through the 'Plugins' menu in WordPress by clicking "BlogActivityShortcode"
4. Go to your Options Panel and open the "BlogActivityShortcode" submenu. /wp-admin/options-general.php?page=BlogActivityShortcode.php
*/
 
/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| BlogActivityShortcode - brief description                                     |
| Copyright (C) 2008, OLT, www.olt.ubc.com                   	     |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |   
|                                                                    |
\--------------------------------------------------------------------/
*/



/**
 * Creation of the BlogActivityShortcodeClass
 * This class should host all the functionality that the plugin requires.
 */
if (!class_exists("BlogActivityShortcodeClass")) {

	class BlogActivityShortcodeClass {
		/**
		 * Global Class Variables
		 */

		var $optionsName = "BlogActivityShortcodeOptions";
		var $folder = '/wp-content/plugins/BlogActivityShortcode/';
		var $version = "0.5";
	

		
		/**
		 * Shortcode Function
		 */
		 function shortcode($atts)
		 {
		  	$postslist = get_posts('numberposts=20');
			
			// lets outpurt some javascript 
			
			
		
			
			$out = "<table width='100%'   cellpadding='4' cellspacing='0' class='sortable' id='display_blog_comments' >
					<thead>
						<tr>
							<th class='sorttable_nosort' ></th>
							<th><strong>Post</strong></th>
							<th><strong>By</strong></th>
							<th><strong>Published</strong></th>
							<th><strong>Comments</strong></th>
							<th><strong>Last Comment</strong></th>
						</tr>
					</thead>
					<tbody>";
					
			$i=0;
			foreach ($postslist as $postObject) : 
				
				$i++;
				$comment_array = get_approved_comments($postObject->ID);
				   
				
				
				$num_comments = count($comment_array);
			
				$published_date = $this->ago($postObject->post_date);
				
				$published_date_raw = strtotime($postObject->post_date);
				
				$comment_date ="none";
				
				if($num_comments != 0)
					$comment_date = $this->ago($comment_array[$num_comments-1]->comment_date);
				
				$comment_date_raw = strtotime($comment_array[$num_comments-1]->comment_date);
				
				
				
				
				
				
				$author = get_userdata($postObject->post_author);
				$author_nickname = $author->user_nicename;
				
				$guid = $postObject->guid;
				
				if($guid == "")
				{	
					global $wpdb;
					$blog_id = $wpdb->blogid;
					$query = "SELECT domain, path FROM $wpdb->blogs WHERE blog_id = $blog_id";
					$resultsInfo = $wpdb->get_results($query, ARRAY_A);
					$guid = "http://".$resultsInfo[0]['domain'].$resultsInfo[0]['path']."?p=".$postObject->ID;
					
				}
				
					$out .="<tr >";				
					$out .="	<td style='border-bottom:1px solid #CCC;' ><a href='#' onclick='toggle(\"toggle_$i\",this); return false;' style='display:block; width:20px;text-align:center; text-decoration:none;'>+</a></td>
								<td style='border-bottom:1px solid #CCC;'><a href='".$guid."' title='".$postObject->post_title."'><strong>".$postObject->post_title."</strong></a></td>
								<td style='border-bottom:1px solid #CCC;'>$author_nickname</td>
								<td style='border-bottom:1px solid #CCC;' sorttable_customkey='$published_date_raw'>$published_date</td>
								<td style='border-bottom:1px solid #CCC;'>$num_comments</td>
								<td style='border-bottom:1px solid #CCC;' sorttable_customkey='$comment_date_raw'>$comment_date</td>
							</tr>";
				
				
					// content  
					$out2 .= "<tr id='toggle_$i' class='blog_activity_content_td'><td colspan='6'><table  style='width:100%;' >";
					$out2 .= "<tr ><td colspan='2' style='padding:2px 10px; border-bottom:1px solid #CCC; margin:0'>".$this->trunc($postObject->post_content)."</td></tr> \n";
				
					
				// comments
					foreach($comment_array as $comment):
						
						
						
						$out2 .= "<tr>
								 <td  style='padding:2px 10px; border-bottom:1px solid #CCC; margin:0' valign='top'>" .$comment->comment_author."</td>
								 <td  style='padding:2px 10px; border-bottom:1px solid #CCC; width:85% margin:0' >".$comment->comment_content."</td>";
						$out2 .= "</tr>";		
					endforeach;
						$out2 .= "</table></td></tr>";
			 	
			 endforeach;
			
		 	$out .= "</tbody></table>";
			
			
			
			
				$out .="<table style='display:none' id='hidden_blog_comments'><tbody>";
				
				$out .= $out2;
				
				$out .="</tbody></table>";
				
				// comments 
			
			return $out;
		 
		 
		 
		 
		 
		 }
		 
		function ago($d) {
		
			$c = getdate();
			
			$p = array('year', 'mon', 'mday', 'hours', 'minutes', 'seconds');
			
			$display = array('year', 'month', 'day', 'hour', 'minute', 'second');
			
			$factor = array(0, 12, 30, 24, 60, 60);
			
			$d = $this->datetoarr($d);
			
			for ($w = 0; $w < 6; $w++) {
			
				if ($w > 0) {
				
				$c[$p[$w]] += $c[$p[$w-1]] * $factor[$w];
				
				$d[$p[$w]] += $d[$p[$w-1]] * $factor[$w];
				
				}
			
				if ($c[$p[$w]] - $d[$p[$w]] > 1) {
				
				return ($c[$p[$w]] - $d[$p[$w]]).' '.$display[$w].'s ago';
				
				}
			}
			return '';
		}

 

		
		
		function datetoarr($d) {
		
			preg_match("/([0-9]{4})(\\-)([0-9]{2})(\\-)([0-9]{2}) ([0-9]{2})(\\:)([0-9]{2})(\\:)([0-9]{2})/", $d, $matches);
		
			return array(
			
			'seconds' => $matches[10],
			
			'minutes' => $matches[8],
			
			'hours' => $matches[6],
			
			'mday' => $matches[5],
			
			'mon' => $matches[3],
			
			'year' => $matches[1],
			
			);
		}
		
		function trunc($str, $words=100)
		{
			$str = strip_tags($str);
			
			$phrase_array = explode(' ',$str);
			
		   if(count($phrase_array) > $max_words && $max_words > 0)
			  $str = implode(' ',array_slice($phrase_array, 0, $max_words)).'...'  ;
		   return $str;
		}
		
		// code to be include in the HTML HEAD
		function head() {
			echo '<script type="text/javascript" src="' . $this->folder . 'sortable.js?ver=' . $this->version . '"></script>' . "\n";
		}
	

	

	} // End Class BlogActivityShortcodePluginSeries

} 







/**
 * Initialize the admin panel function 
 */




if (class_exists("BlogActivityShortcodeClass")) {

	$BlogActivityShortcodeInstance = new BlogActivityShortcodeClass();

}


/**
  * Set Actions, Shortcodes and Filters
  */

if (isset($BlogActivityShortcodeInstance)) {
    
	// Shortcodes
	add_shortcode('blog_activity',array(&$BlogActivityShortcodeInstance, 'shortcode'));
	add_action('wp_head', array(&$BlogActivityShortcodeInstance, 'head'));
	
}