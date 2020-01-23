## What it is

This [RedCap](https://www.project-redcap.org/) plugin adds the @MISSINGCODE action tag. Zero configuration is needed for end-users. 

    This hook can be used to place buttons below a text or notes field to
    populate (and lock) the field with predetermined coded values 
    representing "Missing", "Don't know" etc. Clicking again will blank the
    text box and unlock it. The codes used are determined by the field
    validation that is used, if any. "Integer" and "Number" validation are
    treated as though they had none.
    
    Syntax is @MISSINGCODE=NA,PF,RF,DC,DK,MS
    or for advanced usage...
    @MISSINGCODE=(NA),("Custom_Text","999")
    
                                none      zip           email                     time       phone         date
    NA | Not Applicable       | -6  | 99999-0006 | redcap-noreply@ictr.wisc.edu | 00:00 | 608-555-0106 | 01-01-1906
    PF | Prefer not to answer | -7  | 99999-0007 | redcap-noreply@ictr.wisc.edu | 00:00 | 608-555-0107 | 01-01-1907
    RF | Refused              | -7  | 99999-0007 | redcap-noreply@ictr.wisc.edu | 00:00 | 608-555-0107 | 01-01-1907
    DC | Declined             | -7  | 99999-0007 | redcap-noreply@ictr.wisc.edu | 00:00 | 608-555-0107 | 01-01-1907
    DK | Don't Know           | -8  | 99999-0008 | redcap-noreply@ictr.wisc.edu | 00:00 | 608-555-0108 | 01-01-1908
    MS | Missing              | -9  | 99999-0009 | redcap-noreply@ictr.wisc.edu | 00:00 | 608-555-0109 | 01-01-1909


Only ./framework/resources/missingCodes.php contains unique code, the rest is the stock [RedCap hooks framework](https://github.com/123andy/redcap-hook-framework) with line changes as needed to include this hook. These files are included for ease of install.

## Important Notes

* When mixing with "@CHARLIMIT", "@MISSINGCODE" must be first - This appears to be an issue internal to RedCAp
* The buttons do not display on the "preview instrument" page
* The tag works for both Text and Notes fields
* There is no dodging RedCap's built in validation. If you enable validation on the field then your coded value must be within the specified range.
* Please read the advanced usage section of the end-user help for info on custom button text and coding values.
* See the end-user help for a list of special characters that have known issues.

## To Do

* Rename? It's a pretty bad name.

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
