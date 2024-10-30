=== Indeed API ===
Contributors: bnielsen
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EC26C3EBFD8A8
Tags: jobs, indeed.com, affiliate
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 0.5

Provides simple integration of a WordPress site with the indeed.com API for
job searches.  

== Description ==

Provides shortcode tags that help integrate a WordPress site with the
indeed.com job search engine.  

The plugin uses jQuery for the form events and AJAX calls to perform the job
search so you must have a wp_enqueue_script("jquery"); in your theme
header.php file to ensure jQuery is loaded.  

Use of the indeed.com API requires attribution so there is a shortcode tag,
[indeedattribution] that must be included somewhere in the pages that utilize
the indeed.com API.  


#### Example 1
In this example the attribution, stylesheet, form, search results and a Post A Job link are
created in a WordPress page in four lines of shortcodes and HTML.  

[indeedattribution]  
[indeedsearchstyle]  
[indeedsearchform]  
[indeedsearchresults]  
&lt;a href="[indeedpostjoburl]"&gt;Post A Job&lt;/a&gt;  


#### Attributes
No attributes


#### Shortcodes
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
This shortcode will include the indeed.com attribution HTML, this is a
requirement with the use of the indeed.com API.  


#### Notes
No notes.  

== Installation ==

Unzip the indeed-api.zip file and copy the resulting indeed-api directory to your
WordPress plugins directory.  

Sign up for a publisher indeed.com account and copy the publisher ID into your
WordPress plugin configuration page for the indeed-api plugin.  

If your site will include the ability to post jobs to indeed.com then set up
your Instant Job Site settings on indeed.com and include a link on one of your
posts of pages that uses the [indeedposturl] shortcode as the href value for
the link.  


== Frequently Asked Questions ==

= I have installed and set up the plugin but nothing happens when I click oni the search button, what did I do wrong? =  

The plugin uses jQuery for the form events and AJAX calls so you must have
jQuery enabled in your WordPress installation. Add the following PHP code to
your theme header.php file to make sure jQuery is loaded...  
wp_enqueue_script("jquery");  


= How can I change the animated busy graphics when a search is started? =  

There is a graphic file in the plugin directory, busy.gif, that you can
replace with your own busy.gif animated graphic.  

= How do I change the styling of the form and results? =  

There is an HTML file in the plugin directory named indeedstyle.html that you
can modify to change the appearance of the search form and the search results.  
  
= How do I change the layout of the form and job listing? =  
  
There are a few HTML files in the plugin directory that contain the HTML used
for the form and the results displayed.  
  
indeed-job.html - Contains the HTML for each job displayed.  
indeed-no-jobs.html - Contains the HTML displayed if no jobs are found.  
indeed-search-form.html - Contains the HTML for the search form.  
  
Use caution when editing these HTML files as they contain some special tags
similar to WP shortcodes that are replaced by the plugin.  


== Screenshots ==

1. A job search restricted to the categories specified in the plugin settings.  

== Changelog ==
  
= 0.2 =  
Added a default location option in settings and the option to auto search on
page load. 
  
= 0.3 =  
Added semicolons to the end of Javascript lines to fix a bad interaction with other content plugins.  
  
= 0.4 =  
Converted API query to JSON format. (Now requires PHP version 5.2 or greater)  
Converted API call to CURL to avoid allow_url_fopen problems on some hosting.  
Fixed a bug in the API URL with the unescaped user agent value.  
Moved the HTML into distinct files to simplify the process of customizing the
appearance.  
Changed the admin settings page to make it easier to understand the settings.  
  
= 0.5 =  
Country code was not used in the API URL so the plugin was not functioning for  non-US countries.  
Sorry about that.  



== Upgrade Notice ==
  
= 0.4 =  
Requires CURL and PHP version 5.2 or greater.  


