<?php
include_once('../../../../wp-config.php');
include_once('../../../../wp-load.php');
include_once('../../../../wp-includes/wp-db.php');

// Set the locale to something that's UTF-8 capable
setlocale(LC_ALL, 'en_US.UTF-8');


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
    $result = $wpdb->get_results($query);
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
            $i++;
        }

        $ajaxResp['success']=true;
        $ajaxResp['message']='';
        $ajaxResp['data']=$data;

    }
    echo json_encode($ajaxResp); 
} 

?>