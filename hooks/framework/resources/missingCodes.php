<?php
/**
    This hook can be used to place buttons below a text or notes field to
    populate (and lock) the field with predetermined coded values 
    representing "Missing", "Don't know" etc. Clicking again will blank the
    text box and unlock it. The codes used are determined by the field
    validation that is used, if any. "Integer" and "Number" validation are
    treated as though they had none.
    
    Syntax is @MISSINGCODE=NA,PF,RF,DC,DK,MS
    or for advanced usage...
    @MISSINGCODE=(NA),("Custom_Text","999")
    
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
        margin-bottom: 3px;
        display: inline-block;
        padding = 2px;
    }
</style>
<script type='text/javascript'>
function missingCodeClicked(missingItem, field, code) {
    // The button that was already clicked was clicked again. Toggle it off
    if ($('#' + missingItem + '_' + field).hasClass("stateSelected")) {
        $('#' + missingItem + '_' + field).removeClass("stateSelected");
        $('[name="' + field + '"]').prop("readonly", false);
        $('[name="' + field + '"]').val("");
        $('[name="' + field + '"]').removeClass("fieldDisabled");
    }
    
    // A button was clicked for the first time. Turn all the others off except for the one clicked.
    else {
        $.each($("button[id$='_" + field + "']"), function(_,btn) {
            $(btn).removeClass("stateSelected");
        });
        $('#' + missingItem + '_' + field).addClass("stateSelected");
        $('[name="' + field + '"]').val(code);
        $('[name="' + field + '"]').prop('readonly', true);
        $('[name="' + field + '"]').addClass("fieldDisabled");
    }
}

function injectCode( html, field, code ) {
    html = html.replace(/CODE/g, (''+code).split("'").join("\\x27"));

    if ($('[name="' + field + '"]').val() == code) {
        html = html.replace(/CHKD/g, "stateSelected");
        $('[name="' + field + '"]').prop('readonly', true);
        $('[name="' + field + '"]').addClass("fieldDisabled");
    }
    else 
        html = html.replace(/CHKD/g, "");
    
    // Insert for Date/Time
    if( (typeof $('[name="' + field + '"]').attr("fv") !== 'undefined') && 
        ($('[name="' + field + '"]').attr("fv").startsWith("date") || $('[name="' + field + '"]').attr("fv").startsWith("time") ))
        $('[name="' + field + '"]').nextAll('[class="df"]').after(html);

    // Insert for all others
    else
        $('[name="' + field + '"]').after(html);
}

$(document).ready(function() {
    var affected_fields = <?php print json_encode($hook_functions[$term]) ?>;
    $.each(affected_fields, function(field,args) {
        const template = '<div class="missingCodeButton"><button id="MC_FLD" class="btn btn-defaultrc btn-xs fsl1 CHKD" type="button" onclick="missingCodeClicked(\'MC\',\'FLD\',\'CODE\')">TITLE</button></div>';
        const coding = [ {sym:"NA",code:-6,zipcode:"99999-0006",email:"na@fake.wisc.edu",time:"00:00",date:"01/01/1906",phone:"",text:"Not Applicable"},
                         {sym:"PF",code:-7,zipcode:"99999-0007",email:"pf@fake.wisc.edu",time:"00:00",date:"01/01/1907",phone:"",text:"Prefer not to answer"},
                         {sym:"RF",code:-7,zipcode:"99999-0007",email:"rf@fake.wisc.edu",time:"00:00",date:"01/01/1907",phone:"",text:"Refused"},
                         {sym:"DC",code:-7,zipcode:"99999-0007",email:"dc@fake.wisc.edu",time:"00:00",date:"01/01/1907",phone:"",text:"Declined"},
                         {sym:"DK",code:-8,zipcode:"99999-0008",email:"dk@fake.wisc.edu",time:"00:00",date:"01/01/1908",phone:"",text:"Don't Know"},
                         {sym:"MS",code:-9,zipcode:"99999-0009",email:"ms@fake.wisc.edu",time:"00:00",date:"01/01/1909",phone:"",text:"Missing"} ]
        
        // Parse the input to the tag, format: [["DK"],["PS"],["button_text","code_value"]]
        parsed_args = args.params.match(/\((.*?)\)/g)
        if(parsed_args == null)
            args = args.params.split(",").map(s => [s.toUpperCase()])
        else {
            args = parsed_args.map(s => s.slice(1,-1)).map( function(a) {
                if(a.length == 2) return [a.toUpperCase()];
                else return a.split(/,(?=(?:(?:[^'"]*(?:'|")){2})*[^'"]*$)/).map(s => s.slice(1,-1));
            });
        }
        
        // Loop through every pair of arguments 
        $.each(args, function(_,arg) { 
            // Using built in symbols etc
            if( arg.length == 1 ) {
                $.each(coding, function(_,codeObj) { 
                    if( arg[0] == codeObj.sym ) {
                        insertCode = template.replace(/MC/g, codeObj.sym).replace(/FLD/g, field).replace(/TITLE/g, codeObj.text);
                        
                        // Replace w/ correct code
                        var codeStr = codeObj.code;
                        if( (typeof $('[name="' + field + '"]').attr("fv") !== 'undefined') ) {
                            switch( $('[name="' + field + '"]').attr("fv") ) {
                                case "zipcode":
                                case "time":
                                case "email":
                                case "phone":
                                    codeStr = codeObj[$('[name="' + field + '"]').attr("fv")];
                                break;
                                case "number":
                                case "integer":
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
                                default:
                                    codeStr = ""    
                            }
                        }
                        injectCode( insertCode, field, codeStr );
                    }
                });
            }
            // Using custom text & code ["Text","Code"]
            else if( arg.length == 2 ) {
                injectCode( template.replace(/MC/g, arg[0]).replace(/FLD/g, field).replace(/TITLE/g, arg[0].split("_").join(" ")), field, arg[1] );
            }
        });
    });
});
</script>