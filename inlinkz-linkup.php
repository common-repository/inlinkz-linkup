<?php
/*
  Plugin Name: InLinkz LinkUp
  Plugin URI: http://blog.inlinkz.com/2012/03/wordpress-org-plugin-released/
  Description: Inlinkz Plugin is a widget that is inserted in your wordpress blog and allows you to receive link submissions from your readers.
  Version: 1.2
  Author: InLinkz.com
  Author URI: http://www.inlinkz.com
  License: GPLv2 or later.
 */

/* Copyright 2011 Aris Korbetis & Dimitris Papaioannou (email: wp@inlinkz.com) */

include_once 'InLinkzCollections.php';
include_once 'InLinkzLinks.php';
include_once 'InLinkzImages.php';
include_once(ABSPATH . 'wp-includes/pluggable.php');

global $wpdb;
$inlinkz_colDBTbl = $wpdb->prefix . "inlinkzcollections";
$inlinkz_linksDBTbl = $wpdb->prefix . "inlinkzlinks";

wp_enqueue_script('jquery');

if ($_POST['command'] == "addLink") {   //ADD LINK
    $inlinkz_collectionId = $_POST['collectionId'];
    $inlinkz_linkName     = $_POST['linkName'];
    $inlinkz_linkURL      = $_POST['linkURL'];
    $inlinkz_linkEmail    = $_POST['linkEmail'];
    inlinkz_add_link($inlinkz_collectionId, $inlinkz_linkName, $inlinkz_linkURL, $inlinkz_linkEmail);
    exit;
} else if ($_POST['command'] == "deleteLink"){
    $linkID = $_POST['link_id'];
    inlinkz_delete_link($linkID);
}

//All starts here
function inlinkz_load() {
    global $wpdb;
    $inlinkz_colDBTbl = $wpdb->prefix . "inlinkzcollections";
    $inlinkz_linksDBTbl = $wpdb->prefix . "inlinkzlinks";
    $inlinkz_version = "1.2";

    //setup the database tables
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); //in order for dbDelta to work
    if (get_option("inlinkz_version") === false || get_option("inlinkz_version") < $inlinkz_version)
        $inlinkz_DBtable = "CREATE TABLE " . $inlinkz_colDBTbl . " (
                id INT(11) NOT NULL auto_increment,
                collection_name VARCHAR(255) NOT NULL,
                collection_start DATETIME NOT NULL,
                collection_end DATETIME NOT NULL,
                collection_columns VARCHAR(255) DEFAULT 'three' NOT NULL,
                collection_style VARCHAR(255) DEFAULT 'name' NOT NULL,
                collection_url_length INT(3) DEFAULT '1' NOT NULL,
                collection_name_length INT(3) DEFAULT '50' NOT NULL,
                collection_email VARCHAR(255) NULL,
                collection_notify VARCHAR(3) DEFAULT 'no' NOT NULL,
                collection_review_links VARCHAR(3) DEFAULT 'on' NOT NULL,
                UNIQUE KEY id (id)
                );";

    dbDelta($inlinkz_DBtable);

    if (get_option("inlinkz_version") === false || get_option("inlinkz_version") < $inlinkz_version)
        $inlinkz_DBtable = "CREATE TABLE " . $inlinkz_linksDBTbl . " (
                id INT(11) NOT NULL auto_increment,
                collection_id INT(11) NOT NULL,
                link_name VARCHAR(255) NOT NULL,
                link_url VARCHAR(512) NOT NULL,
                link_email VARCHAR(255) NOT NULL,
                link_publish VARCHAR(3) DEFAULT 'No' NOT NULL,
                link_cookie VARCHAR(255) NOT NULL,
                UNIQUE KEY id (id)
                );";

    dbDelta($inlinkz_DBtable);
    
    //setup version number
    if (get_option("inlinkz_version") === false) {
        add_option("inlinkz_version", $inlinkz_version);
    } elseif (get_option("inlinkz_version") && get_option("inlinkz_version") < $inlinkz_version) {
        update_option("inlinkz_version", $inlinkz_version);
    }
    
    //check for available addons
    checkImagesAddOn();
}

function inlinkz_menu() {
    if (function_exists("add_menu_page")) {
        add_menu_page("InLinkz LinkUp", "InLinkz LinkUp", "manage_options", basename(__FILE__), "inlinkz_manage_collections");
    }
}

function inlinkz_manage_collections() {
    $inlinkz_link_id = $_GET['link_id'];
    
    switch ($_GET['command']) {
        case "moderate_link":                               //MODERATE LINK
            $inlinkz_collection_id = $_GET['collection_id'];
            $inlinkz_count = 0;
            $inlinkz_publish = array();
            $inlinkz_delete = array();
            
            foreach ($_GET as $inlinkz_i => $inlinkz_value) {
                if ($inlinkz_count < sizeof($_GET) - 4) {
                    if ($inlinkz_value == 'delete') {
                        $inlinkz_delete[] = $inlinkz_i;
                    } else if ($inlinkz_value == 'publish') {
                        $inlinkz_publish[] = $inlinkz_i;
                    }
                }
                $inlinkz_count++;
            }

            $inlinkz_comma_separated = implode("','", $inlinkz_delete);
            $inlinkz_comma_separated1 = implode("','", $inlinkz_publish);
            $inlinkz_comma_separated = "'" . $inlinkz_comma_separated . "'";
            $inlinkz_comma_separated1 = "'" . $inlinkz_comma_separated1 . "'";

            global $wpdb, $inlinkz_linksDBTbl;

            $query = "DELETE FROM $inlinkz_linksDBTbl WHERE id IN ($inlinkz_comma_separated) AND collection_id=$inlinkz_collection_id;";
            $wpdb->query($query);
            $query = "UPDATE $inlinkz_linksDBTbl SET link_publish='Yes' WHERE id IN ($inlinkz_comma_separated1) AND collection_id=$inlinkz_collection_id;";
            $wpdb->query($query);

            $inlinkz_collection_id = $_GET['collection_id'];
            inlinkz_render_list_and_edit_links($inlinkz_collection_id);
            break;
        case "edit_collection":                             //EDIT COLLECTION
            $inlinkz_collection_id = $_GET['collection_id'];
            $inlinkz_collection_name = "";
            $inlinkz_collection_start = "";
            $inlinkz_collection_end = "";
            $inlinkz_collection_columns = 0;
            $inlinkz_collection_style = "";
            $inlinkz_collection_url_length = 0;
            $inlinkz_collection_name_length = 0;
            $inlinkz_collection_email = "";
            $inlinkz_collection_notify = "";
            $inlinkz_collection_review_links = "";

            inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 2);
            break;
        case "collection_edited":                           //COLLECTION EDITED
            $inlinkz_collection_id = $_GET['collection_id'];
            $inlinkz_collection_name = $_GET['collection_name'];
            $inlinkz_collection_start = $_GET['collection_startyear'] . "-" . $_GET['collection_startmonth'] . "-" . $_GET['collection_startday'] . " " . $_GET['collection_starthour'] . ":" . $_GET['collection_startminutes'];
            $inlinkz_collection_end = $_GET['collection_endyear'] . "-" . $_GET['collection_endmonth'] . "-" . $_GET['collection_endday'] . " " . $_GET['collection_endhour'] . ":" . $_GET['collection_endminutes'];
            $inlinkz_collection_columns = $_GET['collection_columns'];
            $inlinkz_collection_style = $_GET['collection_style'];
            $inlinkz_collection_url_length = $_GET['collection_url_length'];
            $inlinkz_collection_name_length = $_GET['collection_name_length'];
            $inlinkz_collection_email = $_GET['collection_email'];
            $inlinkz_collection_notify = $_GET['collection_notify'];
            $inlinkz_collection_review_links = $_GET['collection_review_links'];
            if (!empty($inlinkz_collection_name)) {
                $inlinkz_result = inlinkz_update_collection($inlinkz_collection_id, $inlinkz_collection_name, $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links);
                if ($inlinkz_result != -1) {
                    echo('<div id="message" class="updated fade"><p><strong>Collection edited.</strong></p></div>');
                    inlinkz_render_collections();
                } else {
                    echo('<div id="message" class="error"><p><strong>Collection name already exists. Please choose a new one.</strong></p></div>');
                    inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 3);
                }
            } else {
                echo('<div id="message" class="error"><p><strong>No collection name has been given.</strong></p></div>');
                inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 3);
            }
            break;
        case "delete_collection":                           //DELETE COLLECTION
            $inlinkz_collection_id = $_GET['collection_id'];
            echo '        <h2></h2>';
            inlinkz_delete_collection($inlinkz_collection_id);
            echo('<div id="message" class="updated fade"><p><strong>Collection deleted.</strong></p></div>');
            inlinkz_render_collections();
            break;
        case "edit_collection_links":                       //EDIT COLLECTION LINKS
            $inlinkz_collection_id = $_GET['inlinkz_collection_id'];
            inlinkz_render_list_and_edit_links($inlinkz_collection_id);
            break;
        case "edit_link":                                   //EDIT LINK
            if ($inlinkz_link_id == "") {
                $inlinkz_link_id = $_GET['inlinkz_link_id'];
            }
            inlinkz_render_edit_link($inlinkz_link_id);
            break;
        case "link_edited":                                 //LINK EDITED
            $inlinkz_value = $_GET['inlinkz_edit_collection'];
            $inlinkz_link_id = $_GET['link_id'];
            $inlinkz_link_name = $_GET['link_name'];
            $inlinkz_link_url = $_GET['link_url'];
            $inlinkz_link_email = $_GET['link_email'];
            $inlinkz_collection_id = $_GET['collection_id'];
            if (strcmp($inlinkz_value, "Cancel") == 0) {
                inlinkz_get_link_count_from_collection_id($inlinkz_collection_id);
            } else if (empty($inlinkz_link_name)) {
                echo('<div id="message" class="error"><p><strong>Empty Link name is not allowed.</strong></p></div>');
            } else if (empty($inlinkz_link_url)) {
                echo('<div id="message" class="error"><p><strong>Empty URL is not allowed.</strong></p></div>');
            } else {
                $inlinkz_result = inlinkz_edit_link($inlinkz_link_id, $inlinkz_link_name, 
                        $inlinkz_link_url, $inlinkz_link_email, $inlinkz_collection_id);
                if ($inlinkz_result != -1) {
                    echo('<div id="message" class="updated fade"><p><strong>Link edited.</strong></p></div>');
                    //render_links();
                } else {
                    echo('<div id="message" class="error"><p><strong>Link URL already exists. Please choose a new one.</strong></p></div>');
                }
            } 
            inlinkz_render_list_and_edit_links($inlinkz_collection_id);
            break;
        case "delete_link":                                 //DELETE LINK
           $inlinkz_collection_id = $_GET['collection_id'];
            
            if (isset($_GET["delete"])) {
                $inlinkz_deleteArray = $_GET["delete"];

                for ($i = "0"; $i < count($inlinkz_deleteArray); $i++) {
                    inlinkz_delete_link($inlinkz_deleteArray[$i]);
                }
                inlinkz_delete_link($inlinkz_link_id);
                if (count($inlinkz_deleteArray) == 1) {
                    echo('<div id="message" class="updated fade"><p><strong>Link deleted.</strong></p></div>');
                } else {
                    echo('<div id="message" class="updated fade"><p><strong>Links deleted.</strong></p></div>');
                }
                inlinkz_render_list_and_edit_links($inlinkz_collection_id);
            }else{
                inlinkz_render_list_and_edit_links($inlinkz_collection_id);
            }
            break;
        case "add_collection":                              //ADD COLLECTION
            $inlinkz_value = $_GET['inlinkz_add_collection'];
            $inlinkz_collection_name = $_GET['collection_name'];
            $inlinkz_collection_start = $_GET['collection_startyear'] . "-" . $_GET['collection_startmonth'] . "-" . $_GET['collection_startday'] . " " . $_GET['collection_starthour'] . ":" . $_GET['collection_startminutes'];
            $inlinkz_collection_end = $_GET['collection_endyear'] . "-" . $_GET['collection_endmonth'] . "-" . $_GET['collection_endday'] . " " . $_GET['collection_endhour'] . ":" . $_GET['collection_endminutes'];
            $inlinkz_collection_columns = $_GET['collection_columns'];
            $inlinkz_collection_style = $_GET['collection_style'];
            $inlinkz_collection_url_length = $_GET['collection_url_length'];
            $inlinkz_collection_name_length = $_GET['collection_name_length'];
            $inlinkz_collection_email = $_GET['collection_email'];
            $inlinkz_collection_notify = $_GET['collection_notify'];
            $inlinkz_collection_review_links = $_GET['collection_review_links'];
            
            if (strcmp($inlinkz_value, "Cancel") == 0) {
                inlinkz_render_collections();
            } else if (!empty($inlinkz_collection_name)) {
                if (strcmp($inlinkz_collection_notify, "on") == 0) {
                    if (strcmp($inlinkz_collection_email, '') == 0) {
                        echo('<div id="message" class="error"><p><strong>You have checked to be notified but you have not provided a valid email address</strong></p></div>');
                        inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, 
                            $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, 
                            $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
                            $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 4);
                    }else{
                        if (filter_var($inlinkz_collection_email, FILTER_VALIDATE_EMAIL)) {
                            $inlinkz_result = inlinkz_add_collection($inlinkz_collection_name, $inlinkz_collection_start, 
                            $inlinkz_collection_end, $inlinkz_collection_columns, $inlinkz_collection_style, 
                            $inlinkz_collection_url_length, $inlinkz_collection_name_length, $inlinkz_collection_email, 
                            $inlinkz_collection_notify, $inlinkz_collection_review_links);
                            if ($inlinkz_result != -1) {
                                echo('<div id="message" class="updated"><p><strong>Collection added.</strong></p></div>');
                                inlinkz_render_collections();
                            }else {
                                echo('<div id="message" class="error"><p><strong>Collection name already exists. Please choose a new one.</strong></p></div>');
                                inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, 
                                    $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, 
                                    $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
                                    $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 1);
                            }
                        }else{
                            echo ('<div id="message" class="error"><p><strong>Email is invalid.</strong></p></div>');
                            inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, 
                                $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, 
                                $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
                                $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 1);
                        }
                    } 
                }else{
                    $inlinkz_result = inlinkz_add_collection($inlinkz_collection_name, $inlinkz_collection_start, 
                        $inlinkz_collection_end, $inlinkz_collection_columns, $inlinkz_collection_style, 
                        $inlinkz_collection_url_length, $inlinkz_collection_name_length, $inlinkz_collection_email, 
                        $inlinkz_collection_notify, $inlinkz_collection_review_links);
                    if ($inlinkz_result != -1) {
                        echo('<div id="message" class="updated"><p><strong>Collection added.</strong></p></div>');
                        inlinkz_render_collections();
                    } else {
                        echo('<div id="message" class="error"><p><strong>Collection name already exists. Please choose a new one.</strong></p></div>');
                        inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, 
                            $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, 
                            $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
                            $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 1);
                    }
                }
            } else {
                echo('<div id="message" class="error"><p><strong>No collection name has been given.</strong></p></div>');
                inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, 
                    $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, 
                    $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
                    $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, 4);
            }
            break;
        case "select_collection":                           //SELECT COLLECTION
            inlinkz_selectCollection();
            break;
        case "render_add_collection":                       //RENDER ADD COLLECTION
            inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, 
                    $inlinkz_collection_start, $inlinkz_collection_end, 
                    $inlinkz_collection_columns, $inlinkz_collection_style, 
                    $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
                    $inlinkz_collection_email, $inlinkz_collection_notify, 
                    $inlinkz_collection_review_links, 1);
            break;
        case "":
        case "render_collections":                          //RENDER COLLECTIONS
            inlinkz_render_collections();
            break;
        case "sent_contact":                                //SENT CONTACT
            $inlinkz_message = $_GET['message'];
            $inlinkz_name = $_GET['name'];
            $inlinkz_customer_mail = $_GET['customer_mail'];
            $referer = $_SERVER['HTTP_REFERER'] . "     " . $_SERVER['SERVER_ADDR'] . "     " . $_SERVER['SERVER_NAME'];
            inlinkz_sent_contact($inlinkz_message, $inlinkz_name, $inlinkz_customer_mail, $referer);
            echo('<div id="message" class="updated fade"><p><strong>Email sent</strong></p></div>');
            inlinkz_render_collections();
            break;
    }
}

function inlinkz_render_inlinkz($inlinkz_attr) {
    global $wpdb, $inlinkz_linksDBTbl;
    $inlinkz_arr = array();
    
    $inlinkz_addlink  = WP_PLUGIN_URL."/inlinkz-linkup/images/addlink.png";
    $inlinkz_submitLink = WP_PLUGIN_URL."/inlinkz-linkup/images/submitLink.png";
    $inlinkz_bin = WP_PLUGIN_URL."/inlinkz-linkup/images/bin.png";
    $inlinkz_collectionId = $inlinkz_attr['id'];
    $inlinkz_style = inlinkz_getCollectionDataFromId($inlinkz_collectionId, "style");
    $inlinkz_url_length = inlinkz_getCollectionDataFromId($inlinkz_collectionId, "url_length");
    $inlinkz_name_length = inlinkz_getCollectionDataFromId($inlinkz_collectionId, "name_length");
    $inlinkz_sqlStr = "SELECT * FROM $inlinkz_linksDBTbl WHERE collection_id='$inlinkz_collectionId' AND link_publish='Yes';";
    $inlinkz_links = $wpdb->get_results($inlinkz_sqlStr);
    $inlinkz_linksNo = inlinkz_get_link_count_from_collection_id($inlinkz_collectionId);
    $inlinkz_columns = inlinkz_getCollectionDataFromId($inlinkz_collectionId, "columns");
    $inlinkz_start_datetime = inlinkz_getCollectionDataFromId($inlinkz_collectionId, "start");
    $inlinkz_end_datetime = inlinkz_getCollectionDataFromId($inlinkz_collectionId, "end");
    $inlinkz_today = current_time('mysql');
    $inlinkz_isActive = 1; //1=willstart 2=runs 3=ended
    
    $inlinkz_start = strtotime($inlinkz_start_datetime);
    $inlinkz_end   = strtotime($inlinkz_end_datetime);
    $inlinkz_now   = strtotime($inlinkz_today);
    
    if ($inlinkz_start > $inlinkz_now){
        $timeremains = inlinkz_getSpareTimeAsString($inlinkz_start);
        $inlinkz_isActive=1;
    }else if ($inlinkz_start < $inlinkz_now && $inlinkz_now < $inlinkz_end){
        $timeremains = inlinkz_getSpareTimeAsString($inlinkz_end);
        $inlinkz_isActive=2;
    }else if ($inlinkz_now > $inlinkz_end){
        $inlinkz_isActive=3;
    } else {
        echo "ERROR TIME";
    }
    
    if (strcmp($inlinkz_columns, "one") == 0) {
        $inlinkz_columns = 1;
    } else if (strcmp($inlinkz_columns, "two") == 0) {
        $inlinkz_columns = 2;
    } else if (strcmp($inlinkz_columns, "three") == 0) {
        $inlinkz_columns = 3;
    } else if (strcmp($inlinkz_columns, "four") == 0) {
        $inlinkz_columns = 4;
    } else if (strcmp($inlinkz_columns, "five") == 0) {
        $inlinkz_columns = 5;
    } else if (strcmp($inlinkz_columns, "six") == 0) {
        $inlinkz_columns = 6;
    } else if (strcmp($inlinkz_columns, "seven") == 0) {
        $inlinkz_columns = 7;
    } else if (strcmp($inlinkz_columns, "eight") == 0) {
        $inlinkz_columns = 8;
    } else if (strcmp($inlinkz_columns, "nine") == 0) {
        $inlinkz_columns = 9;
    } else if (strcmp($inlinkz_columns, "ten") == 0) {
        $inlinkz_columns = 10;
    }

    if ($inlinkz_columns > 0 && $inlinkz_linksNo > 0){
        if ($inlinkz_linksNo < $inlinkz_columns){
           $inlinkz_columns = $inlinkz_linksNo;
        }
        $inlinkz_cellvalue = (100 - 5*$inlinkz_columns) / $inlinkz_columns ;
    }
    
    $inlinkz_cellvalue = intval($inlinkz_cellvalue);
    $inlinkz_ret  = "<HR>";
    $inlinkz_ret .= "<table style=table-layout:fixed border=0 width=100% id='target'>";
    
    $inlinkz_l = 0;
    foreach ($inlinkz_links as $inlinkz_link) {
        $inlinkz_arr[$inlinkz_l] = $inlinkz_link;
        $inlinkz_l++;
    }
    
    if (!strcmp($inlinkz_style, "name")) {
        $inlinkz_l = 0;
        for ($inlinkz_i = 0; $inlinkz_i < $inlinkz_linksNo; $inlinkz_i++) {
            $inlinkz_ret .= "<tr>";
            for ($inlinkz_k = 0; $inlinkz_k < $inlinkz_columns; $inlinkz_k++) {
                if (strcmp($inlinkz_arr[$inlinkz_l]->link_name, "") != 0) {
                    $inlinkz_index = $inlinkz_l + 1;
                    $inlinkz_dot = ". ";
                } else {
                    $inlinkz_index = "";
                    $inlinkz_dot = "";
                }
                if (strcmp($inlinkz_arr[$inlinkz_l]->link_name, "") != 0) {
                    if ($inlinkz_name_length != 1) {
                        $inlinkz_outNAME = $inlinkz_arr[$inlinkz_l]->link_name;
                        $inlinkz_startlength = strlen($inlinkz_outNAME);
                        $inlinkz_outNAME = substr($inlinkz_outNAME, 0, $inlinkz_name_length);
                        if ($inlinkz_startlength > $inlinkz_name_length) {
                            $inlinkz_outNAME = $inlinkz_outNAME . "...";
                        }
                    } else {
                        $inlinkz_outNAME = $inlinkz_arr[$inlinkz_l]->link_name;
                    }
                    if($inlinkz_k + 1 == $inlinkz_columns){
                        $inlinkz_cellvaluelast = 100 - ($inlinkz_cellvalue * $inlinkz_columns) - ($inlinkz_columns * 5);
                        $inlinkz_cellvaluelast = $inlinkz_cellvaluelast + $inlinkz_cellvalue;
                    }else{
                        $inlinkz_cellvaluelast = $inlinkz_cellvalue;
                    }     
                                      
                    $inlinkz_ret .= "<form method='post'>
                                <td width=5% valign=middle align=right><div align=right>" . $inlinkz_index . $inlinkz_dot . "</div></td>
                                <td width=".$inlinkz_cellvaluelast."% valign=middle align=left ><div align=left style=overflow:hidden><a title=" . $inlinkz_arr[$inlinkz_l]->link_name . " href=" . $inlinkz_arr[$inlinkz_l]->link_url . " target=_blank >" . $inlinkz_outNAME . "</a>";
                    
                    $inlinkz_cookieName = "inlinkz_cookie".$inlinkz_arr[$inlinkz_l]->link_name.$inlinkz_arr[$inlinkz_l]->collection_id.$inlinkz_arr[$inlinkz_l]->link_cookie;
                    $inlinkz_encode = base64_encode($inlinkz_cookieName);
                    $inlinkz_encode = rtrim($inlinkz_encode, '=');
                     if (!isset($_COOKIE[$inlinkz_encode])) {
                         $inlinkz_ret .="</div></td></form>";
                    }else if (strcmp($_COOKIE[$inlinkz_encode], $inlinkz_arr[$inlinkz_l]->link_cookie) == 0) {
                        $inlinkz_ret .= "<input type=image title='Delete link'  src=".$inlinkz_bin." width=12px height=12px onclick=\"javascript:if(!confirm('Are you sure you want to delete the link &quot;".$inlinkz_arr[$inlinkz_l]->link_name."&quot;?')){ return false; }\" >
                        <input type='hidden' name='command' value='deleteLink' >
                        <input type='hidden' name='link_id' value='".$inlinkz_arr[$inlinkz_l]->id."' >
                        <input type='hidden' name='collection_id' value='" . $inlinkz_arr[$inlinkz_l]->collection_id . "' >
                        </div>
                        </td>
                        </form>";
                    }                     
                    $inlinkz_l++;
                }
            }
            $inlinkz_ret .= "</tr>";
            $inlinkz_i += $inlinkz_columns - 1;
        }
    } else if (!strcmp($inlinkz_style, "link")) {
        $inlinkz_l = 0;
        for ($inlinkz_i = 0; $inlinkz_i < $inlinkz_linksNo; $inlinkz_i++) {
            $inlinkz_ret .= "<tr>";
            for ($inlinkz_k = 0; $inlinkz_k < $inlinkz_columns; $inlinkz_k++) {
                if (strcmp($inlinkz_arr[$inlinkz_l]->link_url, "") != 0) {
                    $inlinkz_index = $inlinkz_l + 1;
                    $inlinkz_dot = ". ";
                } else {
                    $inlinkz_index = "";
                    $inlinkz_dot = "";
                }
                if (strcmp($inlinkz_arr[$inlinkz_l]->link_url, "") != 0) {
                    if ($inlinkz_url_length != 1) {
                        $inlinkz_outURL = substr_replace($inlinkz_arr[$inlinkz_l]->link_url, '', 0, 7);
                        $inlinkz_startlength = strlen($inlinkz_outURL);
                        $inlinkz_outURL = substr($inlinkz_outURL, 0, $inlinkz_url_length);
                        if ($inlinkz_startlength > $inlinkz_url_length) {
                            $inlinkz_outURL = $inlinkz_outURL . "...";
                        }
                    } else {
                        $inlinkz_outURL = substr_replace($inlinkz_arr[$inlinkz_l]->link_url, '', 0, 7);
                    }
                    
                    if($inlinkz_k + 1 == $inlinkz_columns){
                        $inlinkz_cellvaluelast = 100 - ($inlinkz_cellvalue * $inlinkz_columns) - ($inlinkz_columns * 5);
                        $inlinkz_cellvaluelast = $inlinkz_cellvaluelast + $inlinkz_cellvalue;
                    }else{
                        $inlinkz_cellvaluelast = $inlinkz_cellvalue;
                    }     
                    //echo $cellvaluelast." ";
                    $inlinkz_ret .= "<form method='post'>
                                <td width=5% valign=middle align=right><div align=right>" . $inlinkz_index . $inlinkz_dot . "</div></td>
                                <td width=".$inlinkz_cellvaluelast."% valign=middle align=left ><div align=left style=overflow:hidden><a title=" . $inlinkz_arr[$inlinkz_l]->link_url . " href=" . $inlinkz_arr[$inlinkz_l]->link_url . " target=_blank >" . $inlinkz_outURL . "</a>";
                    $inlinkz_cookieName = "inlinkz_cookie".$inlinkz_arr[$inlinkz_l]->link_name.$inlinkz_arr[$inlinkz_l]->collection_id.$inlinkz_arr[$inlinkz_l]->link_cookie;
                    $inlinkz_encode = base64_encode($inlinkz_cookieName);
                    $inlinkz_encode = rtrim($inlinkz_encode, '=');
                    if (!isset($_COOKIE[$inlinkz_encode])) {
                        $inlinkz_ret .="</div></td></form>";
                    }else if (strcmp($_COOKIE[$inlinkz_encode], $inlinkz_arr[$inlinkz_l]->link_cookie) == 0) {
                        $inlinkz_ret .= "<input type=image title='Delete link'  src=".$inlinkz_bin." width=12px height=12px onclick=\"javascript:if(!confirm('Are you sure you want to delete the link &quot;".$inlinkz_arr[$inlinkz_l]->link_url."&quot;?')){ return false; }\" >
                                 <input type='hidden' name='command' value='deleteLink' >
                                 <input type='hidden' name='link_id' value='".$inlinkz_arr[$inlinkz_l]->id."' >
                                 <input type='hidden' name='collection_id' value='" . $inlinkz_arr[$inlinkz_l]->collection_id . "' >
                                </div>
                                </td>
                             </form>";
                    }
                    $inlinkz_l++;
                }
            }
            $inlinkz_ret .= "</tr>";
            $inlinkz_i += $inlinkz_columns - 1;
        }
    } else if (!strcmp($inlinkz_style, "both")) {
        $inlinkz_l = 0;
        for ($inlinkz_i = 0; $inlinkz_i < $inlinkz_linksNo; $inlinkz_i++) {
            $inlinkz_ret .= "<tr>";
            for ($inlinkz_k = 0; $inlinkz_k < $inlinkz_columns; $inlinkz_k++) {
                if (strcmp($inlinkz_arr[$inlinkz_l]->link_url, "") != 0) {
                    $inlinkz_index = $inlinkz_l + 1;
                    $inlinkz_dot = ". ";
                } else {
                    $inlinkz_index = "";
                    $inlinkz_dot = "";
                }
                if (strcmp($inlinkz_arr[$inlinkz_l]->link_url, "") != 0) {
                    if ($inlinkz_url_length != 1) {
                        $inlinkz_outURL = substr_replace($inlinkz_arr[$inlinkz_l]->link_url, '', 0, 7);
                        $inlinkz_startlength = strlen($inlinkz_outURL);
                        $inlinkz_outURL = substr($inlinkz_outURL, 0, $inlinkz_url_length);
                        if ($inlinkz_startlength > $inlinkz_url_length) {
                            $inlinkz_outURL = $inlinkz_outURL . "...";
                        }
                    } else {
                        $inlinkz_outURL = substr_replace($inlinkz_arr[$inlinkz_l]->link_url, '', 0, 7);
                    }
                }
                if (strcmp($inlinkz_arr[$inlinkz_l]->link_name, "") != 0) {
                    if ($inlinkz_name_length != 1) {
                        $inlinkz_outNAME = $inlinkz_arr[$inlinkz_l]->link_name;
                        $inlinkz_startlength = strlen($inlinkz_outNAME);
                        $inlinkz_outNAME = substr($inlinkz_outNAME, 0, $inlinkz_name_length);
                        if ($inlinkz_startlength > $inlinkz_name_length) {
                            $inlinkz_outNAME = $inlinkz_outNAME . "...";
                        }
                    } else {
                        $inlinkz_outNAME = $inlinkz_arr[$inlinkz_l]->link_name;
                    }
                    
                    $inlinkz_ret .= "<form method='post'>
                        <td width=5% valign=middle align=right><div align=right>" . $inlinkz_index . $inlinkz_dot . "</div></td>
                        <td width=".$inlinkz_cellvaluelast."% valign=middle align=left ><div align=left style=overflow:hidden><a title=" . $inlinkz_arr[$inlinkz_l]->link_url . " href=" . $inlinkz_arr[$inlinkz_l]->link_url . " target=_blank >" . $inlinkz_outURL . "</a> " . $inlinkz_outNAME;
                    $inlinkz_cookieName = "inlinkz_cookie".$inlinkz_arr[$inlinkz_l]->link_name.$inlinkz_arr[$inlinkz_l]->collection_id.$inlinkz_arr[$inlinkz_l]->link_cookie;
                    $inlinkz_encode = base64_encode($inlinkz_cookieName);
                    $inlinkz_encode = rtrim($inlinkz_encode, '=');
                    if (!isset($_COOKIE[$inlinkz_encode])) {
                        $inlinkz_ret .="</div></td></form>";
                    }else if (strcmp($_COOKIE[$inlinkz_encode], $inlinkz_arr[$inlinkz_l]->link_cookie) == 0) {
                        $inlinkz_ret .= "<input type=image title='Delete link'  src=".$inlinkz_bin." width=12px height=12px onclick=\"javascript:if(!confirm('Are you sure you want to delete the link &quot;".$inlinkz_arr[$inlinkz_l]->link_name."&quot;?')){ return false; }\" >
                                 <input type='hidden' name='command' value='deleteLink' >
                                 <input type='hidden' name='link_id' value='".$inlinkz_arr[$inlinkz_l]->id."' >
                                 <input type='hidden' name='collection_id' value='" . $inlinkz_arr[$inlinkz_l]->collection_id . "' >
                                </div>
                                </td>
                             </form>";
                    }
                    $inlinkz_l++;
                }
            }
            $inlinkz_ret .= "</tr>";
            $inlinkz_i += $inlinkz_columns - 1;
        }
    }
    $inlinkz_ret .= "</table>";
    //$pluginDir = plugins_url() . "/" . dirname(plugin_basename(__FILE__));
//////////// input
    $inlinkz_ret .= "<BR><BR>";
    //The images
 
    //The ShowHide() and validateForm() function
    $inlinkz_ret .= "<script type='text/javascript'>
                function ShowHide(divId){
                    if(document.getElementById(divId).style.display == 'none'){
                        document.getElementById(divId).style.display='block';
                    }else{
                        document.getElementById(divId).style.display = 'none';
                    }
                }

                function validateForm(id){
                    var x=document.forms['HiddenDiv'+id]['linkURL'].value;
                    var y=document.forms['HiddenDiv'+id]['linkName'].value;
                    var z=document.forms['HiddenDiv'+id]['linkEmail'].value;
                    var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
                                        
                    if ( x==null || x ==''){
                        alert('URL must be filled out');
                        return false;
                    }else if (y==null || y==''){
                        alert('Name must be filled out');
                        return false;
                    }else if (z==null){
                        return false;
                    }else if (z != ''){
                        if(reg.test(z) == false) {
                            alert('Invalid Email Address');
                            return false;
                        }
                    }
                    alert('Your link will be visible after administrator\'s review');
                }
            </script>";
    //The add your Link button
    if($inlinkz_isActive == 3) {
        $inlinkz_ret .= "( Collection closed )";
        $inlinkz_ret .= "<br>Link tool by <a href='http://www.inlinkz.com/?refId=wp' target='_blank' >Inlinkz</a>";
    }else if ($inlinkz_isActive == 1){
        $inlinkz_ret .= "( Submissions will start in ".$timeremains." )";
        $inlinkz_ret .= "<br>Link tool by <a href='http://www.inlinkz.com/?refId=wp' target='_blank' >Inlinkz</a>";
    } else if ($inlinkz_isActive == 2){
        $inlinkz_ret .= "<a id=addlink" . $inlinkz_collectionId . " onclick =\"javascript : ShowHide('HiddenDiv" . $inlinkz_collectionId . "')\" href='javascript:;' ><img src=" . $inlinkz_addlink . "></a>";
        $inlinkz_ret .= "<BR>";
        
        //The form
        
        $inlinkz_ret .= "<form name=HiddenDiv".$inlinkz_collectionId." id=HiddenDiv".$inlinkz_collectionId." onsubmit='return validateForm(". $inlinkz_collectionId .")' method='post' style='display:none' >";
        $inlinkz_ret .= "<input type='hidden' name='collectionId' value='" . $inlinkz_collectionId . "' />";
        $inlinkz_ret .= "<input type='hidden' name='command' value='addLink' />";
        $inlinkz_ret .= "<table border=0>";
        $inlinkz_ret .= "<tr><td>URL*</td><td><input type=text title='Enter the link URL' name=linkURL> (URL of your blog post)</td></tr>";
        $inlinkz_ret .= "<tr><td>Name*</td><td><input type=text title='Enter the link name' name=linkName></td></tr>";
        $inlinkz_ret .= "<tr><td>Email</td><td><input type=text title ='Enter your email address' name=linkEmail></td></tr>";
        $inlinkz_ret .= "<tr><td></td><td><input type=image width=100px height=20px src=" . $inlinkz_submitLink . " border=0 ></td></tr>";
        $inlinkz_ret .= "</table>";
        $inlinkz_ret .= "</form>";
        $inlinkz_ret .= "( Submissions will close in ".$timeremains." )";
        $inlinkz_ret .= "<br>Link tool by <a href='http://www.inlinkz.com/?refId=wp' target='_blank' >Inlinkz</a>";
    }else{
        echo "ERROR with dates";
    }
    return $inlinkz_ret;
}

add_action('plugins_loaded', 'inlinkz_load');
add_action('admin_menu', 'inlinkz_menu');
add_shortcode('inlinkz', 'inlinkz_render_inlinkz');

////// MEDIA button
function inlinkz_media_button($inlinkz_context) {
    $image_braskaris = WP_PLUGIN_URL."/inlinkz-linkup/images/braskaris.png";
    $inlinkz_url = WP_PLUGIN_URL."/inlinkz-linkup/tb.php?tab=add&amp;height=500&amp;width=640";
//  if (is_ssl()) $url = str_replace( 'http://', 'https://',  $url );
    $inlinkz_media_button = '%s<a href="' . $inlinkz_url . '" class="thickbox" title="' . __('Select Collection', 'inlinkz-linkup') . '"><img src="'.$image_braskaris.'" alt="' . __('Select Collection ', 'inlinkz-linkup') . '"></a>';
    return sprintf($inlinkz_context, $inlinkz_media_button);
}
add_filter("media_buttons_context", 'inlinkz_media_button');
?>