<?php
/*
Plugin Name: indeed.com API web service
Plugin URI: 
Version: 0.5
Description: A Plugin that provides tools to utilize the indeed.com web services.
Author: Bryan Nielsen
Author URI: 


Copyright 2012 Bryan Nielsen
bnielsen1965@gmail.com

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

/*
The theme header.php must include wp_enqueue_script("jquery"); in the HTML <head> section.

Always include an [indeedattribution] shortcode tag in your indeed.com jobs page for 
proper attribution as required by indeed.com.
*/

/*
shortcodes:
[indeedsearchstyle]
This shortcode will include the indeed search stylesheet in your page to
control the appearance of the form and results.

[indeedsearchform]
This shortcode will include the indeed.com search form on the page.

[indeedsearchresults]
This shortcode includes a div tag that is used to display the search results
using an AJAX call.

[indeedpostjoburl]
This shortcode will produce an URL pointing to the indeed.com post a job page
used for your affiliate job postings.

[indeedattribution]
This shortcode will include the indeed.com attribution HTML, this is a requirement 
with the use of the indeed.com API.
*/

// add a filter to content for the indeed code
add_filter('the_content', 'indeed_search_filter', 99);

// content filter that applies the jobtrend chart
function indeed_search_filter($content) {
  // load options
  $publisher = get_option('indeed_api_publisher');
  $channel = get_option('indeed_api_channel');
  $limit = get_option('indeed_api_search_limit');
  if( $limit === FALSE ) $limit = 10;
  else $limit = intval($limit);
  $country = get_option('indeed_api_country');
  if( $country === FALSE ) $country = 'US';
  $radius = get_option('indeed_api_radius');
  if( $radius === FALSE ) $radius = 25;
  else $radius = intval($radius);
  $sort = get_option('indeed_api_sort');
  $sitetype = get_option('indeed_api_site_type');
  $jobtype = get_option('indeed_api_job_type');
  $fieldtype = get_option('indeed_api_field_type');
  $fieldvalue = get_option('indeed_api_field_value');
  $defaultlocation = get_option('indeed_api_default_location');
  $autosearch = get_option('indeed_api_auto_search');
  $apiversion = get_option('indeed_api_version');
  if( $apiversion === FALSE ) $apiversion = '2';

  $newcontent = $content;

  // if there is a indeed search results request then process
  if( preg_match('/\[indeedsearchresults\]/', $newcontent) ) {
    $apiview = '';

    // set up javascript variables for search
    $apiview .= "<script type=\"text/javascript\" >\n";
    $apiview .= 'var wpajaxurl = "'.admin_url('admin-ajax.php').'";'."\n";
    $apiview .= 'var wpsiteurl = "'.get_option('siteurl').'";'."\n";
    $apiview .= 'var autosearch = ' . ($autosearch ? 'true' : 'false') . ";\n";
    $apiview .= "</script>\n";

    // include the javascript functions
    $indeedjs .= file_get_contents(dirname(__FILE__).'/indeedsearchjs.html');
    $apiview .= $indeedjs;

    // now the div that will hold the results
    $apiview .= '<div id="indeedsearchresults"></div>'."\n";

    $newcontent = preg_replace('/\[indeedsearchresults\]/', $apiview, $newcontent);
  }

  // if there is an indeed search form request then process
  if( preg_match('/\[indeedsearchform\]/', $newcontent) ) {
    $searchform = file_get_contents(WP_PLUGIN_DIR . '/indeed-api/indeed-search-form.html');

    // post start value
    $post_start = (isset($_POST['start'])?$_POST['start']:1);
    $searchform = preg_replace('/\[post_start\]/', $post_start, $searchform);

    // what form field
    if( $fieldtype === 'restricted' ) {
      $what_field = '<select name="q" style="margin:0;">';
      $fv = explode(',', $fieldvalue);
      foreach( $fv as $v ) {
        if( strlen($v) > 0 ) $what_field .= '<option value="'.htmlspecialchars($v).'">'.htmlspecialchars($v).'</option>';
      }
      $what_field .= '</select>';
    }
    else {
      $what_field = '<input type="text" name="q" value="'.htmlspecialchars($fieldvalue).'">';
    }
    $searchform = preg_replace('/\[what_field\]/', $what_field, $searchform);

    // where default value
    $searchform = preg_replace('/\[where_default\]/', $defaultlocation, $searchform);

    // add the search form to the content
    $newcontent = preg_replace('/\[indeedsearchform\]/', $searchform, $newcontent);
  }

  // if the style sheet is requested
  if( preg_match('/\[indeedsearchstyle\]/', $newcontent) ) {
    $indeedstyle = file_get_contents(dirname(__FILE__).'/indeedstyle.html');
    $newcontent = preg_replace('/\[indeedsearchstyle\]/', $indeedstyle, $newcontent);
  }

  // if the post a job link is requested
  if( preg_match('/\[indeedpostjoburl\]/', $newcontent) ) {
    $indeedpostjoburl = 'http://www.indeed.com/p/postjob.php?pid='.$publisher;
    $newcontent = preg_replace('/\[indeedpostjoburl\]/', $indeedpostjoburl, $newcontent);
  }

  // when requested include the indeed.com attribution HTML
  if( preg_match('/\[indeedattribution\]/', $newcontent) ) {
    $indeedattribution = file_get_contents(dirname(__FILE__).'/indeedattribution.html');
    $newcontent = preg_replace('/\[indeedattribution\]/', $indeedattribution, $newcontent);
  }

  return $newcontent;
}



// add the api response code
add_action('wp_ajax_indeedcallback', 'indeed_api_callback');
add_action('wp_ajax_nopriv_indeedcallback', 'indeed_api_callback');

// indeed api ajax callback
function indeed_api_callback() {
  // determine if parameters were posted
  $parameters = array();
  if( isset($_POST['q']) ) $q = $_POST['q'];
  else $q = "";

  if( isset($_POST['l']) ) $l = $_POST['l'];
  else $l = "";

  if( isset($_POST['start']) ) $start = intval($_POST['start']) - 1;
  else $start = 0;
  if( $start < 0 ) $start = 0;

  $publisher = get_option('indeed_api_publisher');
  $channel = get_option('indeed_api_channel');
  $limit = get_option('indeed_api_search_limit');
  if( $limit === FALSE ) $limit = 10;
  else $limit = intval($limit);
  $country = get_option('indeed_api_country');
  if( $country === FALSE ) $country = 'US';
  $radius = get_option('indeed_api_radius');
  if( $radius === FALSE ) $radius = 25;
  else $radius = intval($radius);
  $sort = get_option('indeed_api_sort');
  $sitetype = get_option('indeed_api_site_type');
  $jobtype = get_option('indeed_api_job_type');
  $fieldtype = get_option('indeed_api_field_type');
  $fieldvalue = get_option('indeed_api_field_value');
  $apiversion = get_option('indeed_api_version');
  if( $apiversion === FALSE ) $apiversion = '2';


  // build the search url
  $apiurl = "http://api.indeed.com/ads/apisearch?";

  $apiurl .= "co=" . $country . "&";
  $apiurl .= "publisher=" . $publisher . "&";
  $apiurl .= "q=".urlencode($q)."&";
  $apiurl .= "l=".urlencode($l)."&";
  $apiurl .= "limit=".$limit."&";
  $apiurl .= "sort=".$sort."&";
  $apiurl .= "start=".$start."&";
  $apiurl .= "v=".$apiversion."&";

  $apiurl .= "userip=" . urlencode($_SERVER['REMOTE_ADDR']) . "&";
  $apiurl .= "useragent=" . urlencode($_SERVER['HTTP_USER_AGENT']);

  $apiurl .= "&format=json";


  // make call
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_URL, $apiurl);
  curl_setopt($ch,CURLOPT_TIMEOUT, 120);
  $pageresults = curl_exec($ch);
  curl_close($ch);

  // convert json to object
  $searchresults = json_decode($pageresults);

  // build the return json
  $json = array();
  $errors = array();
  $message = array();

  $json['apiurl'] = $apiurl;

  // check to see if we have some results to return
  if( $searchresults && $searchresults->results && count($searchresults->results) > 0 ) {
    // values used for pagination
    $json['totalresults'] = $searchresults->totalResults;
    $json['startresults'] = $searchresults->start;
    $json['endresults'] = $searchresults->end;
    $json['pages'] = ceil($json['totalresults'] / $limit);
    $json['currentpage'] = floor($json['startresults'] / $limit);

    $pagination = 'Viewing ' . $json['startresults'] . ' through ' . 
                  ($limit + $json['startresults'] - 1 > $json['totalresults'] ? $json['totalresults'] : $limit + $json['startresults'] - 1) . 
                  ' of ' . $json['totalresults'] . ' results<br />';

    $startpage = $json['currentpage'] - 5;
    if( $startpage < 1 ) $startpage = 1;

    for( $i = $startpage; $i <= $json['pages'] && $i - $startpage < 10; $i++ ) {
      if( $i == $startpage ) $pagination .= 'Page: ';

      if( $json['currentpage'] == $i - 1 ) {
        $pagination .= $i."&nbsp;&nbsp;&nbsp;";
      }
      else {
        $pagination .= '<a href="#" onclick="setstart('.(($i - 1) * $limit + 1).'); return false;">'.$i.'</a>&nbsp;&nbsp;&nbsp;';
      }
    }
    $json['pagination'] = $pagination;


    // results html
    $jobhtml = file_get_contents(WP_PLUGIN_DIR . '/indeed-api/indeed-job.html');
    $html = '';

    // loop through job results grabbing defined elements
    $elements = array('url', 'jobtitle', 'company', 'city', 'state', 'country', 'source', 'date', 'snippet', 'onmousedown');
    foreach( $searchresults->results as $jobresult ) {
      $resultelements = array();
      foreach( $elements as $element ) {
        $resultelements[$element] = $jobresult->$element;
      }

      // build on to html with resulting element values
      if( isset($resultelements['url']) && isset($resultelements['jobtitle']) ) {
        $tmphtml = $jobhtml;
        $tmphtml = preg_replace('/\[result_url\]/', $resultelements['url'], $tmphtml);
        $tmphtml = preg_replace('/\[result_jobtitle\]/', $resultelements['jobtitle'], $tmphtml);
        $tmphtml = preg_replace('/\[result_onmousedown\]/', (isset($resultelements['onmousedown']) ? $resultelements['onmousedown'] : ''), $tmphtml);
        $tmphtml = preg_replace('/\[result_city\]/', (isset($resultelements['city']) ? $resultelements['city'] : ''), $tmphtml);
        $tmphtml = preg_replace('/\[result_state\]/', (isset($resultelements['state']) ? $resultelements['state'] : ''), $tmphtml);
        $tmphtml = preg_replace('/\[result_date\]/', (isset($resultelements['date']) ? $resultelements['date'] : ''), $tmphtml);
        $tmphtml = preg_replace('/\[result_company\]/', (isset($resultelements['company']) ? $resultelements['company'] : ''), $tmphtml);
        $tmphtml = preg_replace('/\[result_snippet\]/', (isset($resultelements['snippet']) ? $resultelements['snippet'] : ''), $tmphtml);
        $html .= $tmphtml;
      }
    }
    $json['html'] = $html;

  }
  else {
    $html = file_get_contents(WP_PLUGIN_DIR . '/indeed-api/indeed-no-jobs.html');
    $json['html'] = $html;
  }

  echo json_encode($json);

  die();
}




// if an admin is loading the admin menu then call the admin actions function
if( is_admin() ) add_action('admin_menu', 'indeed_api_admin_actions');

// actions to perform when the admin menu is loaded
function indeed_api_admin_actions() {
  add_options_page("IndeedAPI", "IndeedAPI", 1, "IndeedAPI", "indeed_api_admin");
}

// function called when jobtrend is selected from the admin menu
function indeed_api_admin() {
  include('indeed_api_options.php');
}




?>
