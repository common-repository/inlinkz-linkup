<?php
//if uninstall not called from WordPress exit
    if(!defined('WP_UNINSTALL_PLUGIN')){
        exit();
    }else{
        //delete added database tables
        global $wpdb;
        $inlinkz_colDBTbl = $wpdb->prefix . "inlinkzcollections";
        $inlinkz_linksDBTbl = $wpdb->prefix . "inlinkzlinks";
        
        $wpdb->query("DROP TABLE $inlinkz_colDBTbl , $inlinkz_linksDBTbl");
        
        //delete added options
        delete_option('inlinkz_version');
        delete_option('inlinkz_imagesAddOn');
    }
?>