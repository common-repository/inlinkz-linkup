<?php
include_once 'InLinkzCollections.php';
include_once 'InLinkzLinks.php';

//Checks for dublication before a link or a collection is created
function inlinkz_checkDublication($inlinkz_collORlink, $inlinkz_collection_id, $inlinkz_collORlink_name, $inlinkz_link_id)
{
    global $wpdb, $inlinkz_colDBTbl, $inlinkz_linksDBTbl;
    
    if (strcmp($inlinkz_collORlink, "collections") == 0) {
        $inlinkz_query = "SELECT * FROM $inlinkz_colDBTbl WHERE collection_name = '$inlinkz_collORlink_name' AND id <> '$inlinkz_collection_id' ";
    } else if (strcmp($inlinkz_collORlink, "links") == 0) {
        $inlinkz_query = "SELECT * FROM $inlinkz_linksDBTbl WHERE link_url = '$inlinkz_collORlink_name' AND collection_id = '$inlinkz_collection_id' AND id <>'$inlinkz_link_id' ;";
    } else {
        echo "checkDublication variables must be 'collections' or 'links'";
    }
    $inlinkz_result = $wpdb->get_row($inlinkz_query);
    if ($inlinkz_result != null) {
        return "-1"; //name exists in the database 
    }
    return $inlinkz_result;
}

//make the html code for the date picker. $name can be "start" or "end"
function inlinkz_date_picker($inlinkz_name, $inlinkz_collection_id, $inlinkz_collection_startend, $inlinkz_addedit)
{
    $inlinkz_months = array('', 'January', 'February', 'March', 'April', 'May',
        'June', 'July', 'August', 'September', 'October', 'November', 'December');

    $inlinkz_date = inlinkz_getCollectionDataFromId($inlinkz_collection_id, $inlinkz_name);
    
    if ($inlinkz_addedit == 1) {
        if (!strcmp($inlinkz_name, "start"))
            $inlinkz_nextweek = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
        else if (!strcmp($inlinkz_name, "end"))
            $inlinkz_nextweek = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 7, date("Y"));
        $inlinkz_newdate = date("Y-m-d H:i:s", $inlinkz_nextweek);
        $inlinkz_date_arr = date_parse($inlinkz_newdate);
    }else if (($inlinkz_addedit == 3 || $inlinkz_addedit == 4) && strcmp($inlinkz_collection_startend, "")) {
        $inlinkz_date_arr = date_parse($inlinkz_collection_startend);
    } else if ($inlinkz_addedit == 2) {
        $inlinkz_date_arr = date_parse($inlinkz_date);
    } else {
        echo "BUG STHN date_picker";
    }

    // Month dropdown
    $inlinkz_html = "<select name='collection_" . $inlinkz_name . "month'>";
    for ($inlinkz_i = 1; $inlinkz_i <= 12; $inlinkz_i++) {
        if ($inlinkz_i == $inlinkz_date_arr[month]) {
            $inlinkz_selected = "selected='selected'";
        } else {
            $inlinkz_selected = "";
        }
        $inlinkz_html .="<option value='$inlinkz_i'" . $inlinkz_selected . ">$inlinkz_months[$inlinkz_i]</option>";
    }
    $inlinkz_html .="</select>";

    // Day dropdown
    $inlinkz_html .="<select name='collection_" . $inlinkz_name . "day'>";
    for ($inlinkz_i = 1; $inlinkz_i <= 31; $inlinkz_i++) {
        if ($inlinkz_i == $inlinkz_date_arr[day]) {
            $inlinkz_selected = "selected='selected'";
        } else {
            $inlinkz_selected = "";
        }
        $inlinkz_html .="<option value='$inlinkz_i'" . $inlinkz_selected . ">$inlinkz_i</option>";
    }
    $inlinkz_html .="</select>";

    // Year dropdown
    $inlinkz_html .="<select name='collection_" . $inlinkz_name . "year'>";
    
    for ($inlinkz_i = date("Y"); $inlinkz_i <= date("Y") + 50; $inlinkz_i++) {
        if ($inlinkz_i == $inlinkz_date_arr[year]) {
            $inlinkz_selected = "selected='selected'";
        } else {
            $inlinkz_selected = "";
        }
        $inlinkz_html .="<option value='$inlinkz_i'" . $inlinkz_selected . " >$inlinkz_i</option>";
    }
    $inlinkz_html .="</select>";
    return $inlinkz_html;
}

//make the html code for the time picker. $name can be "start" or "end"
function inlinkz_time_picker($inlinkz_name, $inlinkz_collection_id, $inlinkz_collection_startend, $inlinkz_addedit) 
{
    $inlinkz_time = inlinkz_getCollectionDataFromId($inlinkz_collection_id, $inlinkz_name);
    if ($inlinkz_addedit == 1) {
        if (!strcmp($inlinkz_name, "start"))
        //$inlinkz_nextweek = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));
            $inlinkz_nextweek = current_time('timestamp');
        else if (!strcmp($inlinkz_name, "end"))
        //$inlinkz_nextweek = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 7, date("Y"));
            $inlinkz_nextweek = current_time('timestamp');
        $inlinkz_newdate = date("Y-m-d H:i:s", $inlinkz_nextweek);
        $inlinkz_time_arr = date_parse($inlinkz_newdate);
    }else if (($inlinkz_addedit == 3 || $inlinkz_addedit == 4) && strcmp($inlinkz_collection_startend, "")) {
        $inlinkz_time_arr = date_parse($inlinkz_collection_startend);
    } else if ($inlinkz_addedit == 2) {
        $inlinkz_time_arr = date_parse($inlinkz_time);
    } else {
        echo "BUG STHN date_picker";
    }

    $inlinkz_html = "<select name='collection_" . $inlinkz_name . "hour'>";
    for ($inlinkz_i = 0; $inlinkz_i <= 23; $inlinkz_i++) {
        if ($inlinkz_i == $inlinkz_time_arr[hour]) {
            $inlinkz_selected = "selected='selected'";
        } else {
            $inlinkz_selected = "";
        }
        if ($inlinkz_i <= 9) {
            $inlinkz_i = "0" . $inlinkz_i;
        }
        $inlinkz_html .="<option value='$inlinkz_i'" . $inlinkz_selected . " >$inlinkz_i</option>";
    }
    $inlinkz_html .="</select> : ";

    $inlinkz_html .="<select name='collection_" . $inlinkz_name . "minutes'>";
    for ($inlinkz_i = 0; $inlinkz_i <= 59; $inlinkz_i++) {
        if ($inlinkz_i == $inlinkz_time_arr[minute]) {
            $inlinkz_selected = "selected='selected'";
        } else {
            $inlinkz_selected = "";
        }
        if ($inlinkz_i <= 9) {
            $inlinkz_i = "0" . $inlinkz_i;
        }
        $inlinkz_html .="<option value='$inlinkz_i'" . $inlinkz_selected . " >$inlinkz_i</option>";
    }
    return $inlinkz_html;
}

//prepare select option
function inlinkz_select($inlinkz_select_name, $inlinkz_collection_id, $inlinkz_collection_value, $inlinkz_addedit) 
{
    if (!strcmp($inlinkz_select_name, "collection_columns")) { //COLUMNS
        $inlinkz_collection_columns = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "columns");
        $inlinkz_selectValue = array('one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten');
        $inlinkz_selectName = array('One column', 'Two columns', 'Three columns', 'Four columns',
            'Five columns', 'Six columns', 'Seven columns', 'Eight columns',
            'Nine columns', 'Ten columns');
        $inlinkz_html = "<select name='collection_columns'>";
        for ($inlinkz_i = 0; $inlinkz_i < 10; $inlinkz_i++) {
            if ($inlinkz_addedit == 1) {
                if (!strcmp($inlinkz_selectValue[$inlinkz_i], "three")) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 2) {
                if (!strcmp($inlinkz_collection_columns, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 3 || $inlinkz_addedit == 4) {
                if (!strcmp($inlinkz_collection_value, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else {
                echo "BUG sthn select sta columns";
            }
            $inlinkz_html .="<option value='$inlinkz_selectValue[$inlinkz_i]'" . $inlinkz_selected . ">$inlinkz_selectName[$inlinkz_i]</option>";
        }
    } else if (!strcmp($inlinkz_select_name, "collection_style")) { //STYLE
        $inlinkz_collection_style = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "style");
        $inlinkz_selectValue = array('both', 'link', 'name');
        $inlinkz_selectName = array('Show both the link and its name', 'Show only the link',
            'Show name only (but follow the link)');
        $inlinkz_html = "<select name='collection_style'>";
        for ($inlinkz_i = 0; $inlinkz_i < 3; $inlinkz_i++) {
            if ($inlinkz_addedit == 1) {
                if (!strcmp($inlinkz_selectValue[$inlinkz_i], "name")) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 2) {
                if (!strcmp($inlinkz_collection_style, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 3 || $inlinkz_addedit == 4) {
                if (!strcmp($inlinkz_collection_value, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            }
            $inlinkz_html .="<option value='$inlinkz_selectValue[$inlinkz_i]'" . $inlinkz_selected . ">$inlinkz_selectName[$inlinkz_i]</option>";
        }
    } else if (!strcmp($inlinkz_select_name, "collection_url_length")) { //URL_LENGTH
        $inlinkz_collection_url_length = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "url_length");
        $inlinkz_selectValue = array('1', '10', '20', '30', '40', '50', '75', '100');
        $inlinkz_selectName = array('No limit', '10 chars', '20 chars', '30 chars', '40 chars',
            '50 chars', '75 chars', '100 chars',);
        $inlinkz_html = "<select name='collection_url_length'>";
        for ($inlinkz_i = 0; $inlinkz_i < 8; $inlinkz_i++) {
            if ($inlinkz_addedit == 1) {
                if (!strcmp($inlinkz_selectValue[$inlinkz_i], 1)) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 2) {
                if (!strcmp($inlinkz_collection_url_length, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 3 || $inlinkz_addedit == 4) {
                if (!strcmp($inlinkz_collection_value, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            }
            $inlinkz_html .="<option value='$inlinkz_selectValue[$inlinkz_i]'" . $inlinkz_selected . ">$inlinkz_selectName[$inlinkz_i]</option>";
        }
    } else if (!strcmp($inlinkz_select_name, "collection_name_length")) { //URL_LENGTH
        $inlinkz_collection_name_length = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "name_length");
        $inlinkz_selectValue = array('1', '10', '20', '30', '40', '50', '75', '100');
        $inlinkz_selectName = array('No limit', '10 chars', '20 chars', '30 chars', '40 chars',
            '50 chars', '75 chars', '100 chars',);
        $inlinkz_html = "<select name='collection_name_length'>";
        for ($inlinkz_i = 0; $inlinkz_i < 8; $inlinkz_i++) {
            if ($inlinkz_addedit == 1) {
                if (!strcmp($inlinkz_selectValue[$inlinkz_i], 50)) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 2) {
                if (!strcmp($inlinkz_collection_name_length, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            } else if ($inlinkz_addedit == 3 || $inlinkz_addedit == 4) {
                if (!strcmp($inlinkz_collection_value, $inlinkz_selectValue[$inlinkz_i])) {
                    $inlinkz_selected = "selected='selected'";
                } else {
                    $inlinkz_selected = "";
                }
            }
            $inlinkz_html .="<option value='$inlinkz_selectValue[$inlinkz_i]'" . $inlinkz_selected . ">$inlinkz_selectName[$inlinkz_i]</option>";
        }
    }
    $inlinkz_html .="</select>";
    return $inlinkz_html;
}

function inlinkz_notify($inlinkz_collection_id, $inlinkz_addedit, $inlinkz_collection_notify) {
    if ($inlinkz_addedit == 1) {
        $inlinkz_collection_notify = "";
    } else if ($inlinkz_addedit == 2) {
        $inlinkz_collection_notify = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "notify");
    }
    $inlinkz_ret = "<input type='checkbox' name='collection_notify'";
    if (strcmp($inlinkz_collection_notify, "on") == 0) {
        $inlinkz_ret .= " value='on' checked />";
    } else if (strcmp($inlinkz_collection_notify, "") == 0) {
        $inlinkz_ret .= " value='on' />";
    }
    return $inlinkz_ret;
}

function inlinkz_sent_contact($inlinkz_message, $inlinkz_name, $inlinkz_customer_mail, $extra) {
    $inlinkz_to = "wp@inlinkz.com";
    $inlinkz_headers = "Inlinkz Wordpress";
    $inlinkz_subject = "Inlinkz Customer Support";
    $inlinkz_message = "Email from: '" . $inlinkz_customer_mail . "'\n\nCustomer name: " . $inlinkz_name . "\n\nMessage:\n" . $inlinkz_message . "\n\n$extra";
    mail($inlinkz_to, $inlinkz_subject, $inlinkz_message, $inlinkz_headers);
}

//get the date and time from the database and show it
function inlinkz_render_date($inlinkz_collection_id, $inlinkz_startend) {
    $inlinkz_months_arr = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'May',
                       'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

    $inlinkz_datetime = inlinkz_getCollectionDataFromId($inlinkz_collection_id, $inlinkz_startend);
    $inlinkz_datetime_arr = date_parse($inlinkz_datetime);
    $inlinkz_month = $inlinkz_datetime_arr[month];

    if ($inlinkz_datetime_arr[hour] < 10) {
        $inlinkz_datetime_arr[hour] = "0" . $inlinkz_datetime_arr[hour];
    }
    if ($inlinkz_datetime_arr[minute] < 10) {
         $inlinkz_datetime_arr[minute] = "0" . $inlinkz_datetime_arr[minute];
    }

    $inlinkz_dt = $inlinkz_months_arr[$inlinkz_month] . "-" . $inlinkz_datetime_arr[day] . "-" . $inlinkz_datetime_arr[year] . " @ " . $inlinkz_datetime_arr[hour] . ":" . $inlinkz_datetime_arr[minute];
    echo $inlinkz_dt;
}

function inlinkz_getSpareTimeAsString($timestamp) {
    $remainingSeconds = ($timestamp - strtotime(current_time('mysql')));
    $daySeconds = 60 * 60 * 24;
    $hourSeconds = 60 * 60;
    $minuteSeconds = 60;

    $ret = "";

    $days = floor($remainingSeconds / $daySeconds);

    if ($days > 1) {

        $ret .= $days . "d ";
    } else if ($days > 0) {
        $ret .= "1d ";
    }

    $remainingSeconds -= $daySeconds * $days;

    $hours = floor($remainingSeconds / $hourSeconds);

    if ($hours > 1) {
        $ret .= $hours . "h ";
    } else if ($hours > 0) {
        $ret .= "1h ";
    } else {
        if ($days >= 1) {
            $ret .= "0h ";
        }
    }

    $remainingSeconds -= $hourSeconds * $hours;

    $minutes = floor($remainingSeconds / $minuteSeconds);

    if ($minutes > 1) {
        $ret .= $minutes . "m";
    } else if ($minutes > 0) {
        $ret .= "1m";
    } else {
        $ret .= "0m";
    }

    return $ret;
}
?>