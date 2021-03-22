<?php
/*
Template Name: Case Database

*/
?>


<?php

$template     = get_template();
$theme_root   = get_theme_root( $template );
$template_dir = "$theme_root/$template";

get_header();

$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );



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
    displayExceptionMsg("This page is not available");
    get_footer();
    exit();
}

?>

<div id="main-content">
    <article>
        <div class="entry-content">
            <div class="et-l et-l--post">
                <div class="et_builder_inner_content et_pb_gutters3">
                    <div class="et_pb_with_border et_pb_section et_pb_section_0 et_section_regular flex-container">
                        <div class="et_pb_row et_pb_row_0">
                            <div class="et_pb_column et_pb_column_4_4 et_pb_column_0  et_pb_css_mix_blend_mode_passthrough">
                                <div class="et_pb_module et_pb_text et_pb_text_0 et_pb_bg_layout_light">
                                    <div class="et_pb_text_inner"><h3>Case Database</h3>
                                        <h1>Search our database of court cases.</h1>
                                        <div id="cr_description" style="text-align : justify !important;">
                                            <p>One area of our work is to track the implementation and enforcement of the UK's anti-corruption laws to ensure that they are deterring and preventing corruption. 
                                            We actively monitor corruption cases in the UK Courts and maintain an up-to-date database thereof.
                                            </p>
                                            <p>
                                              This tool allows you to search through our vast database. You can either search for a case by its title, or you can use the advanced search options.  
                                            </p>
                                        </div>
                                    </div>
                                </div> <!-- .et_pb_text -->
                            </div> <!-- .et_pb_column -->
                        </div> <!-- .et_pb_row -->

                        <div class="et_pb_row et_pb_row_1">
                            <div class="et_pb_column et_pb_column_4_4 et_pb_column_1  et_pb_css_mix_blend_mode_passthrough">
                                <div class="et_pb_with_border et_pb_module et_pb_blog_0 et_pb_posts et_pb_bg_layout_light ">
                                    <div class="et_pb_ajax_pagination_container">
                                        <article id="post-2655" class="et_pb_post clearfix et_pb_blog_item_0_0 post-2655 post type-post status-publish format-standard has-post-thumbnail hentry category-resources">
                                        <div class="post-content">
                                            <div class="post-content-inner">
                                                  <input id="cr_case_title" name="cr_case_title" type="text"  placeholder="Start typing...." >
                                                  <div id="cr_search_results" style="display:none"></div>
                                            </div >
                                        </div>	
                                        </article> <!-- .et_pb_post -->
                                    </div>
                                    <div class="et_pb_container">
                                     <a href="#" class="cr_collapsible_lnk">Advanced Search (+)</a>
                                      <div id="cr_adv_opts_frm">
                                        <form id="adv_search_frm">
                                              <label class="cr_input_label" id="cr_cats_lbl" for="cr_cats">Case Category</label>
                                              <div class="cr_input">
                                                  <select id="cr_cats" name="cr_cats">
                                                  <option disabled selected value>Select a case category</option>
                                                  <?php
                                              
                                                  $results_cats = $wpdb->get_results ("SELECT * FROM  $categories_table");
                                                  if ($wpdb->last_error) {
                                                    wp_die(); 
                                                  }
                                                  foreach ($results_cats as $category){
                                                    echo "<option value='$category->cat_id'>$category->name</option>"; 
                                                  }
                                                  ?>
                                                  </select>
                                              </div>
                                              <label class="cr_input_label" id="#cr_countries_lbl" for="cr_countries">Countries</label>
                                              <div class="cr_input">
                                                  <select id="cr_countries" name="cr_countries[]" multiple>
                                                  <?php 
                                                  //$wpdb->show_errors(true);
                                                  $result_countries = $wpdb->get_results ("SELECT * FROM  $countries_table");
                                                  if ($wpdb->last_error) {
                                                    wp_die(); 
                                                  }
                                                  foreach ($result_countries as $country){
                                                    echo "<option value='$country->country_id'>$country->name</option>"; 
                                                  } 
                                                  ?>
                                                  </select>
                                                  
                                                  <small><em>To select multiple countries, hold the command key (⌘) on a Mac OR the control key (Ctrl) on a Windows device. To select ALL, press and hold ⌘ or Ctrl and then press 'A'. </em></small>
                                                  
                                              </div>
                                              <button type="submit" name="cr_search" id="cr_search" class=" et_pb_button">Find Cases</button>
                                              <div id="cr_invalid_frm_fb"></div>
                                          </form>  
                                        </div>
                              
                                        <div id="cr_tbl_container">
                                                  <table id="cr_tbl_adv_res">
                                                    <thead>
                                                      <tr>
                                                        <th class="th"></th>
                                                        <th class="th">Case Title</th>
                                                        <th class="th">Category</th>
                                                        <th class="th">Country/Countries</th>
                                                      </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                    </tfoot>
                                                  </table>
                                        </div>

                                    </div>
                                </div> <!-- .et_pb_posts --> 
                            </div> <!-- .et_pb_column -->
                        </div>
                    </div> <!-- .et_pb_section -->	
                </div><!-- .et_builder_inner_content -->
	        </div><!-- .et-l -->
		</div> <!-- .entry-content -->
	</article> <!-- .et_pb_post -->
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script id="CUSTOM-SCRIPT">




$(document).ready(function(){
//ajax - live search
  $("#cr_case_title").keyup(function() {

    var ajax_url = "<?= admin_url('admin-ajax.php'); ?>";
    var title = $('#cr_case_title').val();
    var data = {
                'action': 'search_results',
                'request': 1,
                'title': title
                };

    
    if (title == "") {
      $("#cr_search_results").html("");
      $("#cr_search_results").hide();
    }
    else {
      $.ajax({
        type: 'post',
        url: ajax_url,
        data: data,
        dataType : 'json',

        success: function(ajaxResp) {
            console.log(ajaxResp);
        
          if(ajaxResp.success){
            let i=0;
            let resList='<ul id="cr_results_list">';
            if(ajaxResp.data.length===0){
              resList= resList + "<li> No results found.</li>";
            }
            else{
              for(let i=0; i<ajaxResp.data.length; i++){
                var temp = ajaxResp.data[i].case_id;
    
                resList= resList + "<li><a class='cr_results_list' href='" + ajaxResp.data[i].url + "' id='"+ ajaxResp.data[i].case_id +"'>" + ajaxResp.data[i].title + "</a></li>";
              }
            }
            resList=resList + '</ul>';
            $("#cr_search_results").html(resList).show();
            $("#cr_search_results").css('border','1px solid #A5ACB2');
          } 
        }
      });
    }
  });
});


//Function to show or hide 'Advanced Search' options
$(".cr_collapsible_lnk").click(function(e){
  e.preventDefault();

  if($("#cr_adv_opts_frm").css('display')=='none'){
    $(".cr_collapsible_lnk").html("Advanced Search (-)");
    $("#cr_adv_opts_frm").css('display', 'block');
  }
  else{
    $(".cr_collapsible_lnk").html("Advanced Search (+)");
    $("#cr_adv_opts_frm").css('display', 'none');
  }

});


//Function to tabulate advanced search results
$('#adv_search_frm').submit(function(event){
  event.preventDefault();
  $('#cr_invalid_frm_fb').css('display', 'none')

  if($('#cr_countries').val().length == 0 && ($('#cr_cats').val() == null) ){
    //console.log($('#cr_countries').val().length);
    //console.log($('#cr_cats').val());
    $('#cr_invalid_frm_fb').css('display', 'block');
    $('#cr_invalid_frm_fb').html("<em style='color: #e94e18 !important; font-weight: bold !important;'>Error: Please complete at least one of the input fields.</em>");
    return;
  }

  

  var ajax_url = "<?= admin_url('admin-ajax.php'); ?>";
  var req_payload = {
                'action': 'adv_search_results',
                'request': 1,
                'form_data': $('#adv_search_frm').serialize()
                };            

  $.ajax({
        type: 'post',
        url: ajax_url,
        data: req_payload,
        dataType : 'json',
        success: function(ajaxResp){

          if(ajaxResp.success){

            let i=0;
            let count;
            let resRows='';
            $('#cr_tbl_container').css('display', 'block');
            for(let i=0; i<ajaxResp.data.length; i++){
              count=i+1;
              resRows= resRows + "<tr><td style='text-align: center !important;'>"+ count +"</td>";
              resRows= resRows + "<td ><a class='cr_row_lnk' href='" + ajaxResp.data[i].url + "' id='"+ ajaxResp.data[i].case_id +"'>"+ajaxResp.data[i].title+"</a></td>";
              resRows= resRows + "<td>"+ajaxResp.data[i].category+"</td>";
              resRows= resRows + "<td>"+ajaxResp.data[i].countries+"</td></tr>";
            }
            $('#cr_tbl_adv_res > tbody').html(resRows);
          } 
          else{
            console.log(ajaxResp.message);
          }
        },
        error: function(xhr, status, error){
          var err = eval("(" + xhr.responseText + ")");
          console.log(err);
        }
  });             
});



</script>


<?php

get_footer();








