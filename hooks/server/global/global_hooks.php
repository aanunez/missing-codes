<?php

/**
	
	This is the GLOBAL HOOKS Master File.
	
	It is included in EVERY hook event across your instance.
	You can use a variable called $hook_event to determine whether or not to take action on the call
	
	This file should be located in hooks/global/global_hooks.php
	
	For example:
	
	if ($hook_event == 'redcap_add_edit_records_page') {
		print "<div class='yellow'>A custom hook has been triggered for $hook_event.</div>";
	}
		
	You can use the same code for multiple events, such as:
	
	if ($hook_event == 'redcap_data_entry_form' || $hook_event == 'redcap_survey_page') {
		print "<div class='yellow'>Your entering data.</div>";
	}
**/

global $hook_functions;

// THIS IS AN ARRY OF FILES TO INCLUDE AT THE END OF THE SCRIPT
$includes = array();

// START redcap_survey_page_top
if ($hook_event == 'redcap_survey_page_top') {
} // END redcap_survey_page_top

// START redcap_data_entry_form OR redcap_survey_page
if ($hook_event == 'redcap_data_entry_form' || $hook_event == 'redcap_survey_page') {
    $includes[] = HOOK_PATH_RESOURCES."missingCodes.php";
} // END redcap_data_entry_form OR redcap_survey_page

if ($hook_event == 'redcap_data_entry_form') {
} // END redcap_data_entry_form only

if ($hook_event == 'redcap_survey_page') {
} // END redcap_survey_page only

// Enable the redcap_user_rights hook globally
if ($hook_event == 'redcap_user_rights') {
} // END redcap_user_rights

// Enable the redcap_every_page_top hook globally
if ($hook_event == 'redcap_every_page_top') {
} // END redcap_every_page_top

// INCLUDE ALL OF THE RESOURCES SPECIFIED GLOBALLY
foreach($includes as $file) {
    if (file_exists($file)) {
        include_once $file;
    } else {
        hook_log("Unable to include $file in $hook_function context");
    }
}