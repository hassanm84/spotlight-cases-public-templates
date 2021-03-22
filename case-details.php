<?php
/*
Template Name: Case Details
*/
get_header();


function displayExceptionMsg($message){
    $excpMsg ='
                <div id="main-content">
                <div class="et-l et-l--body">
                    <div class="et_builder_inner_content et_pb_gutters3">
                        <div class="et_pb_section et_pb_section_0_tb_body et_section_regular">
                            <div class="et_pb_row et_pb_row_0_tb_body et_pb_equal_columns">
                                <div class="et_pb_column et_pb_column_2_5 et_pb_column_0_tb_body et_pb_css_mix_blend_mode_passthrough">
                                <h1>'.$message.'</h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';

    echo  $excpMsg;          
}

if(get_query_var('case_id')!==''){
    $caseId=get_query_var('case_id');

    global $wpdb, $wnm_db_version;
	$charset_collate = $wpdb->get_charset_collate();
	$cases_table = $wpdb->prefix .'cr_cases';
	$countries_table = $wpdb->prefix .'cr_countries';
    $categories_table = $wpdb->prefix .'cr_categories';
    $docs_table = $wpdb->prefix .'cr_docs';
    $sections_table = $wpdb->prefix .'cr_sections';
    $updates_table = $wpdb->prefix .'cr_updates';

    $cr_db_tbl_array = [$cases_table, $countries_table, $categories_table, $docs_table, $sections_table, $updates_table];
    $tableNotExists = false;
    foreach($cr_db_tbl_array as $cr_db_tbl){
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $cr_db_tbl ) ) === $cr_db_tbl ) {
            //echo 'Table exists' . ': '. $cr_db_tbl;
        } 
        else {
            //echo 'Table does not exist' . ': '. $cr_db_tbl;
            $tableNotExists=TRUE;
        }
    }

    if($tableNotExists){
        displayExceptionMsg("This page is not available.");
        get_footer();
    }


    //Retrieve basic case information
    $query_cases="SELECT  
        cases.case_id,
        cases.featured_img,
        cases.title,
        cases.author,
        cases.tagline,
        cases.summary,
        cases.dt_created,
        cases.cat_id,
        countries.name AS country,
        countries.country_id,
        categories.name AS cat_name
        FROM www_cr_cases AS cases 
        LEFT OUTER JOIN www_cr_cases_countries AS cases_countries ON cases.case_id = cases_countries.case_id 
        LEFT OUTER JOIN www_cr_countries AS countries ON cases_countries.country_id = countries.country_id 
        LEFT OUTER JOIN www_cr_categories AS categories ON cases.cat_id = categories.cat_id
        WHERE cases.case_id = '". $caseId."'";

    $results_cases = $wpdb->get_results ($query_cases, ARRAY_A);
    
    if ($wpdb->last_error) {
        //var_dump($wpdb->last_error);
        get_footer();
        exit();
    }
    
    //Retrieve all documents
    $results_docs = $wpdb->get_results ("SELECT * FROM  $docs_table WHERE case_id='$case_id'");
    
    if ($wpdb->last_error){
        displayExceptionMsg("An error occured!");
        get_footer();
        exit();
    }

    //Retrieve all custom sections
    $results_sects = $wpdb->get_results ("SELECT * FROM  $sections_table WHERE case_id='$case_id'");
    if ($wpdb->last_error){
        displayExceptionMsg("An error occured!");
        get_footer();
        exit();
    }

    //Retrieve all updates
    $results_updates = $wpdb->get_results ("SELECT * FROM  $updates_table WHERE case_id='$case_id'");
    if ($wpdb->last_error){
        displayExceptionMsg("An error occured!");
        get_footer();
        exit();
    }




}
else{
    ?>
    <div id="main-content">
        <div class="et-l et-l--body">
            <div class="et_builder_inner_content et_pb_gutters3">
                <div class="et_pb_section et_pb_section_0_tb_body et_section_regular">
                    <div class="et_pb_row et_pb_row_0_tb_body et_pb_equal_columns">
                    <div class="et_pb_column et_pb_column_2_5 et_pb_column_0_tb_body et_pb_css_mix_blend_mode_passthrough">
                        <h1>Case was not found!</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    get_footer();
    exit();
}

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );

?>

<div id="main-content">
   <div class="et-l et-l--body">
      <div class="et_builder_inner_content et_pb_gutters3">
         <div class="et_pb_section et_pb_section_0_tb_body et_section_regular">
            <div class="et_pb_row et_pb_row_0_tb_body et_pb_equal_columns">
               <div class="et_pb_column et_pb_column_2_5 et_pb_column_0_tb_body  et_pb_css_mix_blend_mode_passthrough">
                  <div class="et_pb_module et_pb_image et_pb_image_0_tb_body et_pb_image_sticky">
           
                     <span class="et_pb_image_wrap "><img loading="lazy" src="<?php echo $results_cases[0]['featured_img'];?>" class="wp-image-2698"></span>
                  </div>
               </div><!-- .et_pb_column -->
               <div class="et_pb_column et_pb_column_3_5 et_pb_column_1_tb_body  et_pb_css_mix_blend_mode_passthrough et-last-child">
                  <div class="et_pb_module et_pb_text et_pb_text_0_tb_body  et_pb_text_align_left et_pb_bg_layout_light">
                     <div class="et_pb_text_inner">
                        <h1><?php echo $results_cases[0]['title']; ?></h1>
                        
                     
                    </div>
                  </div>
                  <!-- .et_pb_text -->
                  <div class="et_pb_module et_pb_text et_pb_text_1_tb_body  et_pb_text_align_left et_pb_bg_layout_light">
                     <div class="et_pb_text_inner">
                        <p></p>
                     </div>
                  </div>
                  <!-- .et_pb_text -->
                  <div class="et_pb_module et_pb_text et_pb_text_2_tb_body  et_pb_text_align_left et_pb_bg_layout_light">
                     <div class="et_pb_text_inner">Author: <?php echo $results_cases[0]['author']; ?></div>
                  </div>
                  <!-- .et_pb_text -->
               </div><!-- .et_pb_column -->
            </div>
            <!-- .et_pb_row -->
            <div class="et_pb_row et_pb_row_1_tb_body">
               <div class="et_pb_column et_pb_column_4_4 et_pb_column_2_tb_body  et_pb_css_mix_blend_mode_passthrough et-last-child">
                  <div class="et_pb_module et_pb_post_content et_pb_post_content_0_tb_body">
                     <div class="et-l et-l--post">
                        <div class="et_builder_inner_content et_pb_gutters3">
                           <div class="et_pb_section et_pb_section_0 et_section_regular">
                              <div class="et_pb_row et_pb_row_0">
                                 <div class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough et-last-child">
                                    <div class="et_pb_with_border et_pb_module et_pb_text et_pb_text_0  et_pb_text_align_left et_pb_bg_layout_light">
                                       <div class="et_pb_text_inner">
                                          <p><em><?php echo $results_cases[0]['tagline']; ?></em></p>
                                          <h4><span style="text-decoration: underline;"><strong></strong></span></h4>
                                          <h4><span style="text-decoration: underline;"><strong></strong></span></h4>
                                          <h4><span style="text-decoration: underline;"><strong></strong></span></h4>
                                          <h4><span style="text-decoration: underline;"><strong></strong></span></h4>
                                          <h4><span style="text-decoration: underline;"><strong></strong></span></h4>
                                          <h4><span style="text-decoration: underline;"><strong>Court Documents <em>(Click the links to view)</em><br> </strong></span></h4>
                                          <p><span style="text-decoration: underline;"><strong><em></em></strong></span></p>
                     
                                          <?php
                                            $docCount=0;
                                            foreach($results_docs as $doc){
                                         ?>
                                          <p><?php echo "<em>".++$docCount.") </em> "; ?><a href="<?php echo $doc->doc_url; ?>" target="_blank" rel="noopener noreferrer"><em><?php echo $doc->doc_title; ?></em></a></p>
                                    
                                        <?php
                                            }
                                        ?>
                                  
                                       
                                        </div>
                                    </div>
                                    <!-- .et_pb_text -->
                                    <div class="et_pb_module et_pb_text et_pb_text_1  et_pb_text_align_left et_pb_bg_layout_light">
                                        <div class="et_pb_text_inner">
                                            <div style="margin-top: 25px; margin-bottom: 50px;">
                                                <h4><b>Summary</b><b>&nbsp;</b></h4>
                                                <div><?php echo $results_cases[0]['summary']; ?></div>
                                            </div>

                                           
                                          <?php 
                                          foreach($results_sects as $sect){
                                        
                                          ?>

                                            <div style="margin-top: 25px; margin-bottom: 50px;">   
                                                <h4><b><?php echo $sect->section_title; ?></b></h4>
                                                <div><span style="font-weight: 400;"><?php echo $sect->section_det; ?></span></div>
                                            </div>
                                          <?php
                                          }
                                          ?>
                                        </div>
                                    </div><!-- .et_pb_text -->

                                    <div class="et_pb_module et_pb_text et_pb_text_1  et_pb_text_align_left et_pb_bg_layout_light">
                                        <div class="et_pb_text_inner">

                                        <p><h4><b>Progress of the cases in the UK</b></h4></p>
                                          <?php 
                                          $updateCount=0;
                                          foreach($results_updates as $update){
                                        
                                          ?>
                                            <div style="margin-top: 25px; margin-bottom: 50px;">   
                                                <h5><b><u><i><?php echo ++$updateCount.") ".$update->update_title; ?></i></u></b></h5>
                                                <div><span style="font-weight: 400;"><?php echo $update->update_det; ?></span></div>
                                            </div>
                                            <div class="et_pb_module et_pb_divider et_pb_divider_0 et_pb_divider_position_ et_pb_space">
                                                <div class="et_pb_divider_internal">
                                                </div>
                                            </div>
                                          <?php
                                          }
                                          ?>
                                        </div>
                                    </div><!-- .et_pb_text -->
                                 </div>
                                 <!-- .et_pb_column -->
                              </div>
                              <!-- .et_pb_row -->
                           </div>
                           <!-- .et_pb_section -->		
                        </div>
                        <!-- .et_builder_inner_content -->
                     </div>
                     <!-- .et-l -->
                  </div>
                  <!-- .et_pb_post_content -->
               </div>
               <!-- .et_pb_column -->
            </div>
            <!-- .et_pb_row -->
         </div>
         <!-- .et_pb_section -->		
      </div>
      <!-- .et_builder_inner_content -->
   </div>
   <!-- .et-l -->
</div>  
<?php

get_footer();