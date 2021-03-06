<?php

function bbpvotes_post_vote(){
    
    $result = array(
            'success'   => false
    );
    
    if (!isset($_POST['post_id'])) return false;
    
    $action = $_POST['action'];
    $post_id = $_POST['post_id'];
    
    if ( $action=='bbpvotes_post_vote_up' ){
        $nonce = 'vote-up-post_'.$post_id;
    }else if ( $action=='bbpvotes_post_vote_down' ){
        $nonce = 'vote-down-post_'.$post_id;
    }
    
    if( ! wp_verify_nonce( $_POST['_wpnonce'], $nonce ) ) return false;
    
    if ( $action=='bbpvotes_post_vote_up' ){
        $vote = bbpvotes()->do_post_vote($post_id,true);
    }else if ( $action=='bbpvotes_post_vote_down' ){
        $vote = bbpvotes()->do_post_vote($post_id,false);
    }

    if ( !is_wp_error( $vote ) ) {
        $result['success'] = true;
        $score = bbpvotes_get_votes_score_for_post($post_id);
        $votes_count = bbpvotes_get_votes_count_for_post($post_id);
        $result['score_text'] = sprintf(__('总分: %1$d','bbpvotes'),$score);
        $result['score_title'] = sprintf(__('打分人数: %1$d','bbpvotes'),$votes_count);
    }else{
        $result['message'] = $vote->get_error_message();
    }

    header('Content-type: application/json');
    echo json_encode($result);
    die();
  
}


add_action('wp_ajax_bbpvotes_post_vote_up', 'bbpvotes_post_vote');
add_action('wp_ajax_bbpvotes_post_vote_down', 'bbpvotes_post_vote');

?>

