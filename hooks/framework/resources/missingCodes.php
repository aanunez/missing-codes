<?php
/**
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
        const template = '<button id="MC_FLD" class="btn btn-defaultrc btn-xs fsl1 CHKD" type="button" onclick="missingCodeClicked(\'MC\',\'FLD\',\'CODE\')">TITLE</button>';
        const coding = [ {sym:"NA",code:-6,text:"Not Applicable"},
                         {sym:"PF",code:-7,text:"Prefer not to answer"},
                         {sym:"RF",code:-7,text:"Refused"},
                         {sym:"DC",code:-7,text:"Declined"},
                         {sym:"DK",code:-8,text:"Don't Know"},
                         {sym:"MS",code:-9,text:"Missing"} ]
        $.each(coding, function(_,codeObj) { 
            if( args.params.indexOf(codeObj.sym)>-1 ) {
                insertCode = template.replace(/MC/g, codeObj.sym).replace(/FLD/g, field).replace(/TITLE/g, codeObj.text).replace(/CODE/g, codeObj.code);
                if ($('[name="' + field + '"]').val() == codeObj.code) {
                    insertCode = insertCode.replace(/CHKD/g, "stateSelected");
                    $('[name="' + field + '"]').prop('readonly', true);
                    $('[name="' + field + '"]').addClass("fieldDisabled");
                }
                else 
                    insertCode = insertCode.replace(/CHKD/g, "");
                $('[name="' + field + '"]').after(insertCode);   
            }
        });
    });
});
</script>