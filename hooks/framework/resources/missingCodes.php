<?php
/**
    This hook can be used to place buttons below a text or notes field to
    populate (and lock) the field with predetermined coded values 
    representing "Missing", "Don't know" etc. Clicking again will blank the
    text box and unlock it. The codes used are determined by the field
    validation that is used, if any. "Integer" and "Number" validation are
    treated as though they had none.
    
    Syntax is @MISSINGCODE=NA,PF,RF,DC,DK,MS
    
                                none      zip           email         time    phone     date
    NA | Not Applicable       | -6  | 99999-0006 | na@fake.wisc.edu | 00:00 |   X   | 01/01/1900
    PF | Prefer not to answer | -7  | 99999-0007 | pf@fake.wisc.edu | 00:00 |   X   | 01/01/1901
    RF | Refused              | -7  | 99999-0007 | rf@fake.wisc.edu | 00:00 |   X   | 01/01/1901
    DC | Declined             | -7  | 99999-0007 | dc@fake.wisc.edu | 00:00 |   X   | 01/01/1901
    DK | Don't Know           | -8  | 99999-0008 | dk@fake.wisc.edu | 00:00 |   X   | 01/01/1902
    MS | Missing              | -9  | 99999-0009 | ms@fake.wisc.edu | 00:00 |   X   | 01/01/1903

**/

$term = '@MISSINGCODE';

// Enable hook_functions and hook_fields for this plugin (if not already done)
if (!isset($hook_functions)) {
    $file = HOOK_PATH_FRAMEWORK . 'resources/init_hook_functions.php';
    if (file_exists($file)) {
        include_once $file;
        if (!isset($hook_functions)) { // Verify it has been loaded
            hook_log("Unable to load required init_hook_functions", "ERROR"); 
            return; 
        }
    } 
    else {
        hook_log ("Unable to include required file $file while in " . __FILE__, "ERROR");
    }
}

// See if the term defined in this hook is used on this page
if (!isset($hook_functions[$term])) {
    return;
}
$startup_vars = $hook_functions[$term];
?>
<style>
    .stateSelected {
        background-color: #DBF7DF;
    }
    .fieldDisabled {
        background-color: #CECECE;
    }
    .missingCodeButton {
        margin-top: 5px;
        margin-bottom: 5px;
    }
</style>
<script type='text/javascript'>
function missingCodeClicked(missingItem, field, code) {
    const prefixList = ["NA","PF","RF","DC","DK","MS"]
    if ($('#' + missingItem + '_' + field).hasClass("stateSelected")) {
        $('#' + missingItem + '_' + field).removeClass("stateSelected");
        $('[name="' + field + '"]').prop("readonly", false);
        $('[name="' + field + '"]').val("");
        $('[name="' + field + '"]').removeClass("fieldDisabled");
    }
    else {
        $.each(prefixList, function(_,prefix) {  // undo all except for what was clicked
            if (missingItem == prefix) 
                $('#' + prefix + '_' + field).addClass("stateSelected"); 
            else 
                $('#' + prefix + '_' + field).removeClass("stateSelected");
        });
        $('[name="' + field + '"]').val(code);
        $('[name="' + field + '"]').prop('readonly', true);
        $('[name="' + field + '"]').addClass("fieldDisabled");
    }
}

$(document).ready(function() {
    var affected_fields = <?php print json_encode($startup_vars) ?>;
    $.each(affected_fields, function(field,args) {
        var codeStr;
        const template = '<div class="missingCodeButton"><button id="MC_FLD" class="btn btn-defaultrc btn-xs fsl1 CHKD" type="button" onclick="missingCodeClicked(\'MC\',\'FLD\',\'CODE\')">TITLE</button></div>';
        const coding = [ {sym:"NA",code:-6,zipcode:"99999-0006",email:"na@fake.wisc.edu",time:"00:00",date:"01/01/1906",phone:"",text:"Not Applicable"},
                         {sym:"PF",code:-7,zipcode:"99999-0007",email:"pf@fake.wisc.edu",time:"00:00",date:"01/01/1907",phone:"",text:"Prefer not to answer"},
                         {sym:"RF",code:-7,zipcode:"99999-0007",email:"rf@fake.wisc.edu",time:"00:00",date:"01/01/1907",phone:"",text:"Refused"},
                         {sym:"DC",code:-7,zipcode:"99999-0007",email:"dc@fake.wisc.edu",time:"00:00",date:"01/01/1907",phone:"",text:"Declined"},
                         {sym:"DK",code:-8,zipcode:"99999-0008",email:"dk@fake.wisc.edu",time:"00:00",date:"01/01/1908",phone:"",text:"Don't Know"},
                         {sym:"MS",code:-9,zipcode:"99999-0009",email:"ms@fake.wisc.edu",time:"00:00",date:"01/01/1909",phone:"",text:"Missing"} ]
        $.each(coding, function(_,codeObj) { 
            if( args.params.indexOf(codeObj.sym)>-1 ) {
                insertCode = template.replace(/MC/g, codeObj.sym).replace(/FLD/g, field).replace(/TITLE/g, codeObj.text);
               
                // Replace w/ correct code (if validation is on)
                codeStr = codeObj.code;
                if( (typeof $('[name="' + field + '"]').attr("fv") !== 'undefined') ) {
                    switch( $('[name="' + field + '"]').attr("fv") ) {
                        case "zipcode":
                        case "time":
                        case "email":
                        case "phone":
                            codeStr = codeObj[$('[name="' + field + '"]').attr("fv")];
                        break;
                        case "int":
                        case "float":
                            //Nothing to do
                        break;
                        case "date_mdy":
                        case "date_dmy":
                            codeStr = codeObj.date;
                        break;
                        case "datetime_mdy":
                        case "datetime_dmy":
                            codeStr = codeObj.date + " " + codeObj.time;
                        break;
                        case "datetime_seconds_mdy":
                        case "datetime_seconds_dmy":
                            codeStr = codeObj.date + " " + codeObj.time + ":00";
                        break;
                        case "date_ymd":
                            codeStr = codeObj.date.substr(6) + "/" + codeObj.date.substr(0,5);
                        break;
                        case "datetime_ymd":
                            codeStr = codeObj.date.substr(6) + "/" + codeObj.date.substr(0,5) + " " + codeObj.time;
                        break;
                        case "datetime_seconds_ymd":
                            codeStr = codeObj.date.substr(6) + "/" + codeObj.date.substr(0,5) + " " + codeObj.time + ":00";
                        break;
                        case:
                            codeStr = ""    
                    }
                }
                insertCode = insertCode.replace(/CODE/g, codeStr);
                
                // Check if the button is set to the coded value
                if ($('[name="' + field + '"]').val() == codeStr) {
                    insertCode = insertCode.replace(/CHKD/g, "stateSelected");
                    $('[name="' + field + '"]').prop('readonly', true);
                    $('[name="' + field + '"]').addClass("fieldDisabled");
                }
                else 
                    insertCode = insertCode.replace(/CHKD/g, "");
                
                // Insert for Date/Time
                if( (typeof $('[name="' + field + '"]').attr("fv") !== 'undefined') && 
                    ($('[name="' + field + '"]').attr("fv").startsWith("date") || $('[name="' + field + '"]').attr("fv").startsWith("time") ))
                    $('[name="' + field + '"]').nextAll('[class="df"]').after(insertCode);

                // Insert for all others
                else
                    $('[name="' + field + '"]').after(insertCode);
            }
        });
    });
});
</script>