<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

/*=============================================
  = Get the highest value of any meta key =
===============================================*/
function get_end_meta_key( $type = "max", $key = '', $min = false, $max = false)
{
	global $wpdb;
    $cache_key = md5($key . $type . $min . $max); // join args as cache name
    $results = get_transient($cache_key); // check for cached results

	if($results === false){
		$args = array($key);
        $sql = "SELECT " . $type . "( cast( meta_value as UNSIGNED ) ) FROM {$wpdb->postmeta} WHERE meta_key='%s'";
		if($min !== false && $min !== null){
			$sql .= " AND meta_value > %d";
			array_push($args, $min);
		}
		if($max !== false && $max !== null){
			$sql .= " AND meta_value < %d";
			array_push($args, $max);
		}
        $query = $wpdb->prepare( $sql, $args);
		$val = $wpdb->get_var( $query );
		set_transient($cache_key, $val, HOUR_IN_SECONDS); // set cache for an hour
        return $wpdb->get_var( $query );

    }

    return $results;
}


/*=============================================
          = Get lowest level child term =
===============================================*/
function get_lowest_level_post_term($tax = '', $p = false){
	if($p == false){
		$p = get_the_ID();
	}
	$terms = wp_get_post_terms( $p, $tax);

	$deepestTerm = false;
	$maxDepth = -1;
	if ($terms) {
		foreach ($terms as $term) {
		    $ancestors = get_ancestors( $term->term_id, 'location' );
		    $termDepth = count($ancestors);
		    if ($termDepth > $maxDepth) {
		        $deepestTerm = $term;
		        $maxDepth = $termDepth;
		    }
		}
	}
	return $deepestTerm;

}
