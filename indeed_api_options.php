<?php
  if( $_POST['indeed_api_hidden'] == 'Y' ) {
    //Form data sent  
    $publisher = $_POST['indeed_api_publisher'];  
    update_option('indeed_api_publisher', $publisher);
    $channel = $_POST['indeed_api_channel'];
    update_option('indeed_api_channel', $channel);
    $limit = intval($_POST['indeed_api_search_limit']);
    if( $limit == 0 ) $limit = 10;
    update_option('indeed_api_search_limit', $limit);
    $country = $_POST['indeed_api_country'];
    if( strlen($country) == 0 ) $country = 'US';
    update_option('indeed_api_country', $country);
    $radius = intval($_POST['indeed_api_radius']);
    if( $radius == 0 ) $radius = 25;
    update_option('indeed_api_radius', $radius);
    $sort = $_POST['indeed_api_sort'];
    update_option('indeed_api_sort', $sort);
    $sitetype = $_POST['indeed_api_site_type'];
    update_option('indeed_api_site_type', $sitetype);
    $jobtype = $_POST['indeed_api_job_type'];
    update_option('indeed_api_job_type', $jobtype);
    $fieldtype = $_POST['indeed_api_field_type'];
    update_option('indeed_api_field_type', $fieldtype);
    $fieldvalue = $_POST['indeed_api_field_value'];
    update_option('indeed_api_field_value', $fieldvalue);
    $defaultlocation = $_POST['indeed_api_default_location'];
    update_option('indeed_api_default_location', $defaultlocation);
    if( isset($_POST['indeed_api_auto_search']) ) $autosearch = $_POST['indeed_api_auto_search'];
    else $autosearch = 0;
    update_option('indeed_api_auto_search', $autosearch);
    $apiversion = $_POST['indeed_api_version'];
    update_option('indeed_api_version', $apiversion);
    ?>
    <div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
    <?php
  }
  else {
    //Normal page display
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
  }

?>
<div class="wrap">
 <div id="icon-options-general" class="icon32"><br /></div>
 <?php echo "<h2>Indeed API ".__('Settings')."</h2>"; ?>  
 <form name="indeed_api_admin_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">  
  <input type="hidden" name="indeed_api_hidden" value="Y">  
  <?php echo "<h4>API ".__( 'Setting')."</h4>"; ?>  
  <table class="form-table">
  <tr valign="top">
   <td colspan="2">NOTE: The theme header.php must include wp_enqueue_script("jquery"); in the HTML &lt;head&gt; section.</td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Publisher"); ?></th>
   <td><input type="text" name="indeed_api_publisher" value="<?php echo $publisher; ?>" size="20">Your publisher ID from indeed.com.</td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Channel"); ?></th>
   <td><input type="text" name="indeed_api_channel" value="<?php echo $channel; ?>" size="20">(optional)Name of the indeed.com channel for this site.</td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Search Results Limit"); ?></th>
   <td><input type="text" name="indeed_api_search_limit" value="<?php echo $limit; ?>" size="20">(optional, default 10)The maximum number of job postings to show per page.</td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Search Country"); ?></th>
   <td><input type="text" name="indeed_api_country" value="<?php echo $country; ?>" size="20">(optional, default US)The country to search for jobs.</td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Search Radius"); ?></th>
   <td><input type="text" name="indeed_api_radius" value="<?php echo $radius; ?>" size="20">(optional, default 25)The search radius around the location.</td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Sort Criteria"); ?></th>
   <td>
    <select name="indeed_api_sort">
     <option value="date">Date</option>
     <option value="relevance" <?php if( $sort === 'relevance' ) echo 'selected'; ?>>Relevance</option>
    </select>(optional, default date)The sort criteria.
   </td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Site Type"); ?></th>
   <td>
    <select name="indeed_api_site_type">
     <option value=""></option>
     <option value="jobsite" <?php if( $sitetype === 'jobsite') echo 'selected'; ?>>Job Site</option>
     <option value="employer" <?php if( $sitetype === 'employer' ) echo 'selected'; ?>>Employer</option>
    </select>(optional)The type of job web site.
   </td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Job Type"); ?></th>
   <td>
    <select name="indeed_api_job_type">
     <option value=""></option>
     <option value="fulltime" <?php if( $jobtype === 'fulltime') echo 'selected'; ?>>Full Time</option>
     <option value="parttime" <?php if( $jobtype === 'parttime' ) echo 'selected'; ?>>Part Time</option>
     <option value="contract" <?php if( $jobtype === 'contract' ) echo 'selected'; ?>>Contract</option>
     <option value="internship" <?php if( $jobtype === 'internship' ) echo 'selected'; ?>>Internship</option>
     <option value="temporary" <?php if( $jobtype === 'temporary' ) echo 'selected'; ?>>Temporary</option>
    </select>(optional)The type of jobs to list.
   </td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("API Version"); ?></th>
   <td>
    <input type="text" name="indeed_api_version" value="<?php echo $apiversion; ?>" >The indeed.com API version to use. As of this writing version 2 is required.
   </td>
  </tr>

  <tr valign-"top">
    <td colspan="2">
     <hr>
    </td>
  </tr>

 </table>


  <?php echo "<h4>".__( 'Form Setting')."</h4>"; ?>
  <table class="form-table">

  <tr valign="top">
   <td colspan="2">
     The search form <b>What</b> field can be open to search for any job name or it can be restricted to a finite number of job names.<br>
     <br>
     If the <b>What</b> is selected as an open field you can enter a starting job name value to be displayed when the page loads. If the
     <b>What</b> is selected as a restricted field then you <u>must</u> enter a comma separated list of job names for the value of the
     field and this will be converted to a drop down list on the form.
   </td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("<b>What</b> Search Field Type"); ?></th>
   <td>
    <input type="radio" name="indeed_api_field_type" value="open" <?php if( $fieldtype !== 'restricted' ) echo ' checked'; ?>>Open &nbsp;&nbsp;
    <input type="radio" name="indeed_api_field_type" value="restricted" <?php if( $fieldtype === 'restricted' ) echo ' checked'; ?>>Restricted
   </td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("<b>What</b> Field Values"); ?></th>
   <td>
    <input type="text" name="indeed_api_field_value" value="<?php echo $fieldvalue; ?>" ><br>
    Empty or use a starting value when using an Open search <b>What</b> field. A comma separated list of job names when using a 
    restricted <b>What</b> search field.
   </td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Default Location"); ?></th>
   <td>
    <input type="text" name="indeed_api_default_location" value="<?php echo $defaultlocation; ?>" >(optional)Specify a default search location when the page is loaded.
   </td>
  </tr>

  <tr valign="top">
   <th scope="row"><?php _e("Auto Search"); ?></th>
   <td>
    <input type="checkbox" name="indeed_api_auto_search" value="1" <?php echo ($autosearch ? 'checked' : ''); ?>>(optional)Specify that an auto search should be started when the page loads.
   </td>
  </tr>

  </table>

  <p class="submit">  
   <input type="submit" class="button-primary" name="Submit" value="<?php _e('Save Changes') ?>" />  
  </p>  
 </form>  

</div>  
