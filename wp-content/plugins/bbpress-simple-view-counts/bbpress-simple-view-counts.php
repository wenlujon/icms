<?php
/*
Plugin Name: bbpress Simple View Counts
Plugin URI: http://www.blogercise.com
Description: Counts and shows views in bbPress 2 forum
Author: jezza101
Version: 0.1
Author URI: http://www.blogercise.com
*/


/*
Date      Version     History
------    -----       --------



*/

class bbpress_simple_view_counts {

    public function __construct() {

       //FILTERS NEEDED TO SHOW THE VIEWS IN THE FRONT END
       add_action ('bbp_theme_after_topic_started_by', array($this,'show_views_forum_page'),60);
       add_filter('bbp_get_single_topic_description', array($this,'show_views_topic_page'), 100, 2);

    }


    function show_views_forum_page() {

      //FORUM PAGE
      //ie the forum pages lists all the posts in the forum, let's add a view count

      $post_id = get_the_ID();
      $count   = get_post_meta( $post_id, 'bbp_svc_viewcounts', true );

      if (!empty($count)){
//              echo '<br><span class="bbp-topic-started-by">Views: '.$count.'</span>';
	echo $count;
                  }
	else {
		echo 0;
	}
      return;
     }

     function show_views_topic_page( $content, $reply_id ) {


         //First let's update the view count
         $post_id = get_the_ID();

         //get previous count and add one!
         $count = get_post_meta( $post_id, 'bbp_svc_viewcounts', true );
         $count = $count + 1 ;

         //save the new count
         update_post_meta($post_id, 'bbp_svc_viewcounts', $count) ;

         return $content;
     }

}

//Instatiate our plugin class
$bbpress_view_counts  = new bbpress_simple_view_counts();
