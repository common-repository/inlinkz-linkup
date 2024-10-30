<?php
include_once 'InLinkzSupportFuns.php';
include_once 'inlinkz-linkup.php';

function inlinkz_render_list_and_edit_links($inlinkz_collection_id) 
{    
    global $wpdb, $inlinkz_linksDBTbl, $inlinkz_colDBTbl;
    
    $inlinkz_sqlStr = "SELECT * from $inlinkz_linksDBTbl WHERE collection_id='$inlinkz_collection_id' AND link_publish='Yes';";
    $inlinkz_sqlStr1 = "SELECT collection_name from $inlinkz_colDBTbl WHERE id='$inlinkz_collection_id';";
    $inlinkz_sqlStr2 = "SELECT * from $inlinkz_linksDBTbl WHERE collection_id='$inlinkz_collection_id' AND link_publish='No';";

    $inlinkz_collections = $wpdb->get_row($inlinkz_sqlStr1);
    $inlinkz_links = $wpdb->get_results($inlinkz_sqlStr);
    $inlinkz_links_moderate = $wpdb->get_results($inlinkz_sqlStr2);
?>
    <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
        <thead>
            <tr>
                <th scope="col" width="33%"></th>
                <th scope="col" width="33%"><div align="center"><h2>Links of collection  "<?php echo $inlinkz_collections->collection_name; ?>" </h2></th></div>
        <th scope="col" width="33%"></th>
    </tr>
    </thead>
    </table>
    <BR><BR>
<?php
    if (sizeof($inlinkz_links_moderate) > 0) {
?>
        <h3>Links that require moderation</h3>
        <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
            <thead>
                <tr>
                    <th scope="col" width="3%">ID</th>
                    <th scope="col" width="15%">Name</th>
                    <th scope="col" width="28%">URL</th>
                    <th scope="col" width="14%">Email</th>
                    <th scope="col" width="6%">&nbsp;&nbsp;&nbsp;&nbsp;Publish</th>
                    <th scope="col" width="7%">&nbsp;&nbsp;&nbsp;&nbsp;Delete</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th scope="col" width="3%">ID</th>
                    <th scope="col" width="15%">Name</th>
                    <th scope="col" width="28%">URL</th>
                    <th scope="col" width="14%">Email</th>
                    <th scope="col" width="6%">&nbsp;&nbsp;&nbsp;&nbsp;Publish</th>
                    <th scope="col" width="7%">&nbsp;&nbsp;&nbsp;&nbsp;Delete</th>
                </tr>
            </tfoot>
            <tbody>
            <form method="get">
<?php
                foreach ($inlinkz_links_moderate as $inlinkz_link) {
?>
                    <tr class="alternate">
                        <td><?php echo($inlinkz_link->id); ?></td>
                        <td><?php echo($inlinkz_link->link_name); ?></td>
                        <td><a target=_blank href= <?php echo($inlinkz_link->link_url); ?>><?php echo($inlinkz_link->link_url); ?></a></td>
                        <td><?php echo($inlinkz_link->link_email); ?></td>
                        <td align="center">
                            <INPUT TYPE=RADIO VALUE="publish" NAME="<?php echo($inlinkz_link->id); ?>" >
                        </td>
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO VALUE="delete" NAME="<?php echo($inlinkz_link->id); ?>" >
                        </td>
                    </tr>
<?php
                }
?>
                <tr class='alternate'>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <span class='submit'>
                            <input type='submit' value='Update' onclick= "javascript:if(!confirm('Are you sure you want to continue with publishing or deleting the links? Deleted links will be lost!')){ return false; }" />
                            <input type='hidden' name='page' value='inlinkz-linkup.php' />
                            <input type='hidden' name='command' value='moderate_link' />
                            <input type='hidden' name='link_id' value=<?php echo ($inlinkz_link->id) ?> />
                            <input type='hidden' name='collection_id' value=<?php echo ($inlinkz_collection_id) ?> />
                        </span>
                    </td>
                </tr>
          </form>
          </tbody>
        </table>
<?php
    }else
    {
        echo "<h3>No links currently need moderation</h3>";
    }
?>
    <BR><BR>
    <script type='text/javascript'>
//        jQuery(document).ready(function(){
//            jQuery(".button").click(function(){
//                
//                $name = jQuery(this).attr("linkId");
//              alert(window.location);
////                window.location.replace("<?php echo $_SERVER[PHP_SELF]; ?>?page=inlinkz-linkup.php&command=edit_link&link_id=" + $name);
//                window.location = "http://10.0.0.30/wordpress/wp-admin/admin.php?editButton_2=&page=inlinkz-linkup.php&command=delete_link&collection_id=1";
//             
//            });
//        });

    </script>
    <h3>Published links</h3>
    <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
        <thead>
            <tr>
                <th scope="col" width="3%">ID</th>
                <th scope="col" width="15%">Name</th>
                <th scope="col" width="28%">URL</th>
                <th scope="col" width="14%">Email</th>
                <th scope="col" width="4%">Edit</th>
                <th scope="col" width="7%">&nbsp;&nbsp;&nbsp;&nbsp;Delete</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th scope="col" width="3%">ID</th>
                <th scope="col" width="15%">Name</th>
                <th scope="col" width="28%">URL</th>
                <th scope="col" width="14%">Email</th>
                <th scope="col" width="4%">Edit</th>
                <th scope="col" width="7%">&nbsp;&nbsp;&nbsp;&nbsp;Delete</th>
            </tr>
        </tfoot>
        <form method="get" id="linkform" action="" >
            <tbody>
<?php
                foreach ($inlinkz_links as $inlinkz_link) {
?>
                    <tr class="alternate">
                        <td><?php echo($inlinkz_link->id); ?></td>
                        <td><?php echo($inlinkz_link->link_name); ?></td>
                        <td><a target=_blank href=<?php echo($inlinkz_link->link_url); ?>><?php echo($inlinkz_link->link_url); ?></a></td>
                        <td><?php echo($inlinkz_link->link_email); ?></td>
                        <td style="padding:10px;">
                                <a class="button" href="<?php echo $_SERVER[PHP_SELF]; ?>?page=inlinkz-linkup.php&command=edit_link&link_id=<?php echo($inlinkz_link->id); ?>">Edit</a>
                            
                        </td>
                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="delete[]" value=<?php echo($inlinkz_link->id); ?> /></td>
                    </tr>
                <!---</tfoot>-->
<?php
                }
?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <span class='submit'>
                            <input type='submit' value='Delete' />
                            <input type='hidden' name='page' value='inlinkz-linkup.php' />
                            <input type='hidden' name='command' value='delete_link' />
                            <input type='hidden' name='collection_id' value=<?php echo($inlinkz_collection_id) ?> />
                         </span>
                    </td>
                </tr>
            </tbody>
        </form>
    </table>
<?php
}

function inlinkz_edit_link($inlinkz_link_id, $inlinkz_link_name, $inlinkz_link_url, 
        $inlinkz_link_email, $inlinkz_collection_id) 
{
    global $wpdb, $inlinkz_linksDBTbl;

    $inlinkz_result = inlinkz_checkDublication("links", $inlinkz_collection_id, $inlinkz_link_url, $inlinkz_link_id);
        if ($inlinkz_result == -1) 
        {
            return $inlinkz_result;
        }

    //set the cookie
    //$inlinkz_cookievalue = uniqid();
    //$inlinkz_cookieName = "inlinkz_cookie" . $inlinkz_link_name . $inlinkz_collection_id . $inlinkz_cookievalue;
    //$inlinkz_encode = base64_encode($inlinkz_cookieName);
    //$inlinkz_encode = rtrim($inlinkz_encode, '=');
    //setcookie($inlinkz_encode, $inlinkz_cookievalue, time() + (86400 * 365));

    //$query = "UPDATE $inlinkz_linksDBTbl SET link_name='$inlinkz_link_name', link_url='$inlinkz_link_url', link_email='$inlinkz_link_email', link_cookie='$inlinkz_cookievalue' WHERE id='$inlinkz_link_id';";
    $query = "UPDATE $inlinkz_linksDBTbl SET link_name='$inlinkz_link_name', link_url='$inlinkz_link_url', link_email='$inlinkz_link_email' WHERE id='$inlinkz_link_id';";
    //echo $query;
    $wpdb->query($query);
}

//edit a link and add it to the db
function inlinkz_render_edit_link($inlinkz_link_id) {
    global $wpdb, $inlinkz_linksDBTbl;

    $inlinkz_sqlStr = "SELECT * FROM $inlinkz_linksDBTbl WHERE id='$inlinkz_link_id';";
    $inlinkz_link = $wpdb->get_row($inlinkz_sqlStr);
    $inlinkz_collection_id = $inlinkz_link->collection_id;
    ?>
    <form method="get">
        <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
            <thead>
                <tr>
                    <th scope="col" width="33%"></th>
                    <th scope="col" width="33%"><div align="center"><h2>Edit Link</h2></th></div>
            <th scope="col" width="33%"></th>
            </tr>
            </thead>
        </table>
        <BR><BR>
        <table width="100%" border="0" class="form_table" id="inlinkz_add_collection" bgcolor="#EDF2FE">
            <tr>
                <td td width="40%" align="right"><strong>URL*</strong></td>
                <td>
                    <input type="text" name="link_url" id="inlinkz_link_url" style="width: 300px" value="<?php echo $inlinkz_link->link_url; ?>" />
                </td>
            </tr>
            <tr>
                <td td width="40%" align="right"><strong>Name*</strong></td>
                <td>
                    <input type="text" name="link_name" id="inlinkz_link_name" style="width: 300px" value="<?php echo $inlinkz_link->link_name; ?>" />
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><strong>Email</strong></td>
                <td>
                    <input type="text" name="link_email" id="inlinkz_link_email" style="width: 300px" value="<?php echo $inlinkz_link->link_email; ?>" />
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center" valign="middle">
                    <span class=submit><input class=submit name=inlinkz_edit_collection type=submit value=Edit /></span>
                    <input type=hidden name=page value=inlinkz-linkup.php />
                    <input type=hidden name=command value=link_edited />
                    <input type=hidden name=collection_id value="<?php echo $inlinkz_collection_id; ?>" />
                    <input type=hidden name=link_id value="<?php echo $inlinkz_link_id; ?>" />
                    <span class=submit>
                        <input class=submit name=inlinkz_edit_collection type=submit value=Cancel />
                    </span>
                </td>
            </tr>
        </table>
    </form>
<?php
}
  
//counts and returns the number of links in a collection
function inlinkz_get_link_count_from_collection_id($inlinkz_collectionId) 
{
    global $wpdb, $inlinkz_linksDBTbl;

    $inlinkz_sqlStr = "SELECT COUNT(*) as c from $inlinkz_linksDBTbl WHERE collection_id='$inlinkz_collectionId';";

    $inlinkz_collection = $wpdb->get_row($inlinkz_sqlStr);
    return $inlinkz_collection->c;
}

function inlinkz_delete_link($inlinkz_link_id) {
    global $wpdb, $inlinkz_linksDBTbl;

    $inlinkz_delete_query = "DELETE FROM " . $inlinkz_linksDBTbl . " WHERE id=" . $inlinkz_link_id . ";";
    $wpdb->query($inlinkz_delete_query);
}

//adds new link to the DB 
function inlinkz_add_link($inlinkz_collection_id, $inlinkz_linkName, $inlinkz_linkURL, $inlinkz_linkEmail) {
    global $wpdb, $inlinkz_linksDBTbl;
    $inlinkz_dummy_link_id = 0;

    //check if url starts with http:// and add it otherwise
    if (strpos($inlinkz_linkURL, "http://") === false) {
        $inlinkz_linkURL = "http://" . $inlinkz_linkURL;
    } else {
        $inlinkz_position = strrpos($inlinkz_linkURL, "http://");
        if ($inlinkz_position != 0) {
            $inlinkz_linkURL = "http://" . $inlinkz_linkURL;
        }
    }

    $inlinkz_review = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "review");
    if(strcmp($inlinkz_review,"on") == 0 ){
        $inlinkz_linkSubmit = "No";
    }else{
        $inlinkz_linkSubmit = "Yes";
    }
    
    $inlinkz_result = inlinkz_checkDublication("links", $inlinkz_collection_id, $inlinkz_linkURL, $inlinkz_dummy_link_id);
    if ($inlinkz_result != -1) {   
        //set the cookie
        $inlinkz_cookievalue = uniqid();
        $inlinkz_cookieName = "inlinkz_cookie".$inlinkz_linkName.$inlinkz_collection_id.$inlinkz_cookievalue;
        $inlinkz_encode = base64_encode($inlinkz_cookieName);
        $inlinkz_encode = rtrim($inlinkz_encode, '=');
        setcookie($inlinkz_encode,$inlinkz_cookievalue, time() + (86400*365));
        
        $inlinkz_query = "INSERT INTO " . $inlinkz_linksDBTbl . " (collection_id, link_name, link_url, link_email, link_publish, link_cookie) VALUES ('" . $inlinkz_collection_id . "', '" . $inlinkz_linkName . "', '" . $inlinkz_linkURL . "', '" . $inlinkz_linkEmail . "', '" . $inlinkz_linkSubmit . "', '" . $inlinkz_cookievalue . "');";
        $wpdb->query($inlinkz_query);

        $inlinkz_notify = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "notify");
        if (strcmp($inlinkz_notify, "on") == 0) {
            $inlinkz_email = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "email");
            $inlinkz_collection_name = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "name");
            $inlinkz_to = $inlinkz_email;
            $inlinkz_subject = "Inlinkz Wordpress";
            $inlinkz_message = "New link submited to collection: '". $inlinkz_collection_name ."'\n\nLink name: ".$inlinkz_linkName."\nLink URL: ".$inlinkz_linkURL."\nEmail: ".$inlinkz_linkEmail ;
            mail($inlinkz_to, $inlinkz_subject, $inlinkz_message);
        }
        wp_redirect($_SERVER['HTTP_REFERER']);
    } else {
        $inlinkz_ret = "<html><head></head><body style='background-color:#aaaaaa;font-family:sans-serif;font-size:24px;text-align:center'>";
        $inlinkz_ret .= "<BR><BR><BR>URL already exists in collection";
        $inlinkz_ret .= "<BR><BR><button style='width:60px;height:40px;font-size:24;font-family:sans-serif' onclick='location.href=\"" . $_SERVER['HTTP_REFERER'] . "\"'>OK</button>";
        $inlinkz_ret .= "</body></html>";
        echo $inlinkz_ret;
    }
}
?>