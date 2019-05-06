
## What it is

This plugin adds the @MISSINGCODE action tag. Zero configuration is needed for end-users. 

    This hook can be used to place buttons below a text or notes field to
    populate (and lock) the field with predetermined coded values 
    representing "Missing", "Don't know" etc. Clicking again will blank the
    text box and unlock it.
    
    Syntax is @MISSINGCODE=NA,PF,RF,DC,DK,MS
    
    NA | Not Applicable       | -6
    PF | Prefer not to answer | -7
    RF | Refused              | -7
    DC | Declined             | -7
    DK | Don't Know           | -8
    MS | Missing              | -9
    

Only ./framework/resources/missingCodes.php contains unique code, the rest is the stock [RedCap hooks framework](https://github.com/123andy/redcap-hook-framework) with line changes as needed to include this hook. These files are included for ease of install.

## Important Notes

* The buttons do not display on the "preview instrument" page
* The tag DOES work on Notes fields
* When mixing with "@CHARLIMIT", "@MISSINGCODE" must be first (bug?)
* The buttons do not respect the "@READONLY" tag

## Install

To install just drop/merge the the hooks folder into `/var/www/html/`. If you are using other hooks you'll want to merge and correct the `global_hooks.php` file 
    
And set the "REDCap Hooks" setting under "General Configuration" to `var/www/html/hooks/framework/redcap_hooks.php`

You will probably want to add a link to the enduser documentation (`howto_missingcode.html`) somewhere on your RedCap deployment.
    
## File Descriptions

* hooks/framework/redcap_hooks.php
  * No changes, file is as distributed
    
* hooks/framework/hooks_common.php
  * Added the line `define('HOOK_PATH_RESOURCES', dirname(__FILE__).DS."resources".DS);` to included the resources folder in the framework.
    
* hooks/framework/resources/missingCodes.php
  * The plugin itself
    
* hooks/framework/resources/init_hook_functions.php
  * No changes, file is as distributed
    
* hooks/server/global/global_hooks.php
  * Typical global_hooks file with the line `$includes[] = HOOK_PATH_RESOURCES."missingCodes.php";` added under hooks 'redcap_data_entry_form' and 'redcap_survey_page'
