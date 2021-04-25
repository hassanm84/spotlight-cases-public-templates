<?php
/**
 * Divi Cake Child Theme
 * Functions.php
 *
 * ===== NOTES ==================================================================
 * 
 * Unlike style.css, the functions.php of a child theme does not override its 
 * counterpart from the parent. Instead, it is loaded in addition to the parent's 
 * functions.php. (Specifically, it is loaded right before the parent's file.)
 * 
 * In that way, the functions.php of a child theme provides a smart, trouble-free 
 * method of modifying the functionality of a parent theme. 
 * 
 * Discover Divi Child Themes: https://divicake.com/products/category/divi-child-themes/
 * Sell Your Divi Child Themes: https://divicake.com/open/
 * 
 * =============================================================================== */
 
function divichild_enqueue_scripts() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'divichild_enqueue_scripts' );


add_action( 'wp_ajax_search_results', 'search_results_callback' );
add_action( 'wp_ajax_nopriv_search_results', 'search_results_callback');

add_action( 'wp_ajax_adv_search_results', 'adv_search_results_callback' );
add_action( 'wp_ajax_nopriv_adv_search_results', 'adv_search_results_callback');


function cr_case_vars($vars){
	$vars[] = "case_id";
	return $vars;
  }

add_filter( 'query_vars', 'cr_case_vars');


function search_results_callback(){
	global $wpdb, $wnm_db_version;
	$charset_collate = $wpdb->get_charset_collate();
	$cases_table = $wpdb->prefix .'cr_cases';
	$countries_table = $wpdb->prefix .'cr_countries';
	$categories_table = $wpdb->prefix .'cr_categories';

	if(isset($_POST['title'])){
    
		$case_title = $_POST['title'];
	
		$ajaxResp = array();
		$data=array();
		$query="SELECT * FROM ".$cases_table." WHERE title LIKE '%".$case_title."%' AND publish_status='1'";
		$result = $wpdb->get_results ($query);
		if($wpdb->last_error){
			$ajaxResp['success']=false;
			$ajaxResp['message']=$wpdb->last_error;
		}
		else{
			$i=0;
			foreach ($result as $case){
				$data[$i]['case_id']=$case->case_id;
			   
	
				// Convert the codepoints to entities
				$tempStr = preg_replace("/\\\\u([0-9a-fA-F]{4})/", "&#x\\1;", $case->title);
				// Convert the entities to a UTF-8 string
				$data[$i]['title'] = html_entity_decode($tempStr, ENT_QUOTES, 'UTF-8');
				$data[$i]['url'] = esc_url(add_query_arg( 'case_id', $case->case_id, site_url('/case-details')));
				$i++;
			}
	
			$ajaxResp['success']=true;
			$ajaxResp['message']='';
			$ajaxResp['data']=$data;
	
		}
		echo json_encode($ajaxResp); 
	} 

	wp_die(); 

}



function adv_search_results_callback(){

	global $wpdb, $wnm_db_version;
	$charset_collate = $wpdb->get_charset_collate();
	$cases_table = $wpdb->prefix .'cr_cases';
	$countries_table = $wpdb->prefix .'cr_countries';
	$categories_table = $wpdb->prefix .'cr_categories';
	$prosecutors_table = $wpdb->prefix . 'cr_prosecutors';

	$wh_clause_country = $wh_clause_cat = $wh_clause_prosecutor = '1=1';

	$ajaxResp = array();
	$data=array();

	if( isset($_POST['form_data'])  && !empty($_POST['form_data'])){
		parse_str($_POST['form_data'], $unserialised_data);

		if(isset($unserialised_data['cr_countries']) && !empty($unserialised_data['cr_countries'])){
			$countriesArr = $unserialised_data['cr_countries'];
			$countriesList = implode(",", $countriesArr);
			$wh_clause_country = "countries.country_id IN ($countriesList)";
		}
		else{
			//do nothing
		}

		if(isset($unserialised_data['cr_cats']) && $unserialised_data['cr_cats']!==''){
			$category = $unserialised_data['cr_cats'];
			$wh_clause_cat = "cases.cat_id= '$category' ";
		}
		else{
			//do nothing
		}


		if(isset($unserialised_data['cr_cats']) && $unserialised_data['cr_cats']!==''){
			$category = $unserialised_data['cr_cats'];
			$wh_clause_cat = "cases.cat_id= '$category' ";
		}
		else{
			//do nothing
		}

		if(isset($unserialised_data['cr_prosecutors']) && $unserialised_data['cr_prosecutors']!==''){
			$prosecutorId = $unserialised_data['cr_prosecutors'];
			$wh_clause_prosecutor = "cases.prosecutor_id= '$prosecutorId' ";
		}
		else{
			//do nothing
		}

		$query_adv_search = "SELECT 
							cases.case_id,
							cases.title, 
							countries.name AS country, 
							categories.name AS category,
							prosecutors.name AS prosecutor
							FROM $cases_table AS cases 
							LEFT OUTER JOIN www_cr_cases_countries AS cases_countries ON cases.case_id = cases_countries.case_id 
							LEFT OUTER JOIN www_cr_countries AS countries ON cases_countries.country_id = countries.country_id 
							LEFT OUTER JOIN www_cr_categories AS categories ON cases.cat_id = categories.cat_id
							LEFT OUTER JOIN www_cr_prosecutors AS prosecutors ON cases.prosecutor_id = prosecutors.prosecutor_id
							WHERE  $wh_clause_cat AND $wh_clause_prosecutor AND $wh_clause_country AND cases.publish_status='1'";
				
        $result_adv_search = $wpdb->get_results ($query_adv_search);
		
		if($wpdb->last_error){
            $ajaxResp['success']=false;
            $ajaxResp['message']=$wpdb->last_error;
        }
        else{
            processResults($ajaxResp, $data, $result_adv_search);
		}
		
		echo json_encode($ajaxResp); 
	}
	else{
		//do nothing
	}

	wp_die(); 
}


function processResults(&$ajaxResp, &$data, $result){
    $i=0;
    foreach ($result as $case){
		if($i>0 && $case->case_id===$data[$i-1]['case_id']){
			$data[$i-1]['countries'] = $data[$i-1]['countries'].", ".$case->country;
			continue;
		}
		else{
			$data[$i]['url'] = esc_url(add_query_arg( 'case_id', $case->case_id, site_url('/case-details')));
			$data[$i]['case_id']=$case->case_id;
			$data[$i]['title']=$case->title;
			$data[$i]['prosecutor']=$case->prosecutor;
			$data[$i]['category']=$case->category;
			$data[$i]['countries']=$case->country;
		}
        $i++;
    }
    $ajaxResp['success']=true;
    $ajaxResp['message']='';
    $ajaxResp['data']=$data;
}

?>