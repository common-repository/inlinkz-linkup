<?php
include_once 'InLinkzSupportFuns.php';
include_once 'InLinkzLinks.php';

//creates the sql query and updates wp_inlinkz_collection table in the DB
function inlinkz_update_collection($inlinkz_collection_id, $inlinkz_collection_name, 
        $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, 
        $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
        $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links) {
    
    global $wpdb, $inlinkz_colDBTbl;

    $inlinkz_dummy_link_id = 0;
    $inlinkz_result = inlinkz_checkDublication("collections", $inlinkz_collection_id, 
            $inlinkz_collection_name, $inlinkz_dummy_link_id);
    
    if ($inlinkz_result == -1) {
        return $inlinkz_result;
    }

    $inlinkz_query = "UPDATE $inlinkz_colDBTbl SET collection_name='$inlinkz_collection_name'
             , collection_start='$inlinkz_collection_start', collection_end='$inlinkz_collection_end'
             , collection_columns='$inlinkz_collection_columns', collection_style='$inlinkz_collection_style'
             , collection_url_length='$inlinkz_collection_url_length', collection_name_length='$inlinkz_collection_name_length'
             , collection_email='$inlinkz_collection_email', collection_notify='$inlinkz_collection_notify'
             , collection_review_links='$inlinkz_collection_review_links'
             WHERE id='$inlinkz_collection_id';";

    $wpdb->query($inlinkz_query);
}

function inlinkz_render_collections() {
    $inlinkz_collections = inlinkz_get_collections();
    ?>

    <style>
        .widefat td{
            padding: 3px 7px;
            vertical-align: middle;
        }

        .widefat tbody th.check-column{
            padding: 7px 0;
            vertical-align: middle;
        }
    </style>
    
    <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
        <thead>
            <tr>
                <th scope="col" width="33%"></th>
                <th scope="col" width="33%"><div align="center"><h2>InLinkz Collections</h2></th></div>
                <th scope="col" width="33%"></th>
            </tr>
        </thead>
    </table>

    <BR>
    <table id='inlinkz_manage_collections' width='100%'>
        <tr>
            <td align=center>
                <form method='get'>
                    <span class='submit'>
                        <input type='hidden' name='page' value='inlinkz-linkup.php' />
                        <input class='button-secondary' name='inlinkz_add_collection' type='submit' value='Add new collection' /><h2></h2>
                        <input type='hidden' name='command' value='render_add_collection' />
                    </span>
                </form>
            </td>
        </tr>
    </table>
    
    <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
        <thead>
            <tr>
                <th scope="col" width="3%">ID</th>
                <th scope="col" width="12%">Collection name</th>
                <th scope="col" width="5%">Links</th>
                <th scope="col" width="12%">Start date</th>
                <th scope="col" width="12%">End date</th>
                <th scope="col" width="5%">Col</th>
                <th scope="col" width="5%">Style</th>
                <th scope="col" width="6%">URL len</th>
                <th scope="col" width="7%">Name len</th>
                <th scope="col" width="7%">Notify me</th>
                <th scope="col" width="8%">Review links</th>
                <th scope="col" width="4%"></th>
                <th scope="col" width="6%"></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th scope="col" width="3%">ID</th>
                <th scope="col" width="12%">Collection name</th>
                <th scope="col" width="5%">Links</th>
                <th scope="col" width="12%">Start date</th>
                <th scope="col" width="12%">End date</th>
                <th scope="col" width="5%">Col</th>
                <th scope="col" width="5%">Style</th>
                <th scope="col" width="6%">URL len</th>
                <th scope="col" width="7%">Name len</th>
                <th scope="col" width="7%">Notify me</th>
                <th scope="col" width="8%">Review links</th>
                <th scope="col" width="4%"></th>
                <th scope="col" width="6%"></th>
            </tr>
        </tfoot>
        <tbody>

<?php
    foreach ($inlinkz_collections as $inlinkz_collection) {
?>
        <tr class="alternate">
            <td><?php echo($inlinkz_collection->id); ?></td>
            <td><?php echo($inlinkz_collection->collection_name); ?></td>
            <td>
                <form method="get">
                    <span class="submit">
                        <input type="submit" value="<?php echo inlinkz_get_link_count_from_collection_id($inlinkz_collection->id); ?>" />
                        <input type="hidden" name="page" value="inlinkz-linkup.php" />
                        <input type="hidden" name="command" value="edit_collection_links" />
                        <input type="hidden" name="inlinkz_collection_id" value="<?php echo($inlinkz_collection->id); ?>" />
                    </span>
                </form>
            </td>
            <td><?php inlinkz_render_date($inlinkz_collection->id, "start"); ?></td>
            <td><?php inlinkz_render_date($inlinkz_collection->id, "end"); ?></td>
            <td><?php echo ($inlinkz_collection->collection_columns); ?></td>
            <td><?php echo ($inlinkz_collection->collection_style); ?></td>
            <td><?php echo ($inlinkz_collection->collection_url_length); ?></td>
            <td><?php echo ($inlinkz_collection->collection_name_length); ?></td>
            <td><?php
                if (strcmp($inlinkz_collection->collection_notify, "on") == 0) 
                {
                    echo Yes;
                    //echo $inlinkz_collection->collection_email;
                }else{
                    echo No;
                }
?>
           </td>
           <td><?php
               if (strcmp($inlinkz_collection->collection_review_links, "on") == 0) 
                {
                    echo Yes;
                    //echo $inlinkz_collection->collection_email;
                }else{
                    echo No;
                }
?>
           </td>
           <td>
               <form method="get">
                    <span class="submit">
                        <input type="submit" value="Edit" />
                        <input type="hidden" name="page" value="inlinkz-linkup.php" />
                        <input type="hidden" name="command" value="edit_collection" />
                        <input type="hidden" name="collection_id" value="<?php echo($inlinkz_collection->id); ?>" />
                    </span>
                </form>
            </td>
            <td>
                <form method="get">
                    <span class="submit">
                        <input name="inlinkz_delete" type="submit" value="Delete" onclick="javascript:if(!confirm('Are you sure you want to delete the collection &quot;<?php echo($inlinkz_collection->collection_name); ?>&quot;?')){ return false; }"   />
                        <input type="hidden" name="page" value="inlinkz-linkup.php" />
                        <input type="hidden" name="command" value="delete_collection"  />
                        <input type="hidden" name="collection_id" value="<?php echo($inlinkz_collection->id); ?>" />
                    </span>
                </form>
            </td>
            </tr>
<?php
    }
?>
        </tbody>
    </table>
    <br><br>

    <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
        <thead>
            <tr>
                <th scope="col" width="33%"></th>
                <th scope="col" width="33%"><div align="center"><h2>Contact</h2></th></div>
                <th scope="col" width="33%"></th>
            </tr>
        </thead>
    </table>
    <form method="get">
    <table width="100%" border="0" class="form_table" id="inlinkz_add_collection" bgcolor="#EDF2FE">
        <tr>
            <td width="40%" align="right">Message</td>
            <td><textarea name="message" cols="50" rows="3" id="detail"></textarea></td>
        </tr>
        <tr>
            <td width="40%" align="right">My name</td>
            <td><input name="name" type="text" id="name" size="50"></td>
        </tr>
        <tr>
            <td width="40%" align="right">Email</td>
            <td><input name="customer_mail" type="text" id="customer_mail" size="50">
                <span class="submit">
                    <input type="submit" value="Send" />
                    <input type="hidden" name="page" value="inlinkz-linkup.php" />
                    <input type="hidden" name="command" value="sent_contact" />
                </span>
            </td>
        </tr>
    </table>
    </form>
<?php
}

//Get the all the collections from the DB
function inlinkz_get_collections() {
    global $wpdb, $inlinkz_colDBTbl;

    $inlinkz_query = "SELECT * FROM " . $inlinkz_colDBTbl . " ORDER BY id DESC;";
    $inlinkz_collections = $wpdb->get_results($inlinkz_query);

    return $inlinkz_collections;
}

function inlinkz_render_addedit_collection($inlinkz_collection_id, $inlinkz_collection_name, 
        $inlinkz_collection_start, $inlinkz_collection_end, $inlinkz_collection_columns, 
        $inlinkz_collection_style, $inlinkz_collection_url_length, $inlinkz_collection_name_length, 
        $inlinkz_collection_email, $inlinkz_collection_notify, $inlinkz_collection_review_links, $inlinkz_addedit) {
    
    if ($inlinkz_addedit == 2) {
        $inlinkz_collection_name         = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "name");
        $inlinkz_collection_email        = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "email");
        $inlinkz_collection_notify       = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "notify");
        $inlinkz_collection_review_links = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "review");
    }
    ?>
    <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="100%">
        <thead>
            <tr>
                <th scope="col" width="33%" ></th>
                <th scope="col" width="33%"><div align="center"><h2><?php if ($inlinkz_addedit == 1 || $inlinkz_addedit == 4) echo "Add Collection"; else echo "Edit Collection"; ?></h2></th></div>
                <th scope="col" width="33%"></th>
            </tr>
        </thead>
    </table>
    <form name="__inlinkzEditForm" method="get">
        <table class="widefat">
            <tbody>
                <tr>
                    <td class="row-title"><label for="tablecell"></label>Name</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table width="100%" border="0" class="form_table" id="inlinkz_add_collection" bgcolor="#EDF2FE">
            <tr>
                <td width="40%" align="right">*Collection name (displayed on InLinkz menu)</td>
                <td><input type="text" name="collection_name" style="width: 300px"<?php echo('value="' . $inlinkz_collection_name . '"'); ?> /></td>
            </tr>
        </table>
        <table class="widefat">
            <tbody>
                <tr>
                    <td class="row-title"><label for="tablecell"></label>Duration</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <table width="100%" border="0" class="form_table" id="inlinkz_add_collection" bgcolor="#EDF2FE">
            <tr>
                <td width="40%" align="right">Start date</td>
                <td><?php echo inlinkz_date_picker("start", $inlinkz_collection_id, $inlinkz_collection_start, $inlinkz_addedit) ?> @ <?php echo inlinkz_time_picker("start", $inlinkz_collection_id, $inlinkz_collection_start, $inlinkz_addedit) ?></td>
            </tr>
            <tr>
                <td width="40%" align="right">End date</td>
                <td><?php echo inlinkz_date_picker("end", $inlinkz_collection_id, $inlinkz_collection_end, $inlinkz_addedit) ?> @ <?php echo inlinkz_time_picker("end", $inlinkz_collection_id, $inlinkz_collection_end, $inlinkz_addedit) ?></td>
            </tr>
        </table>
        <table class="widefat">
            <tbody>
                <tr>
                    <td class="row-title"><label for="tablecell"></label>Display</td>
                    <td></td>
                </tr>
            </tbody>
        </table>       
        <table width="100%" border="0" class="form_table" id="inlinkz_add_collection" bgcolor="#EDF2FE">
            <tr>
                <td width="40%" align="right">No of columns</td>
                <td><?php echo inlinkz_select("collection_columns", $inlinkz_collection_id, $inlinkz_collection_columns, $inlinkz_addedit); ?></td>
            </tr>
            <tr>
                <td width="40%" align="right">Display style</td>
                <td><?php echo inlinkz_select("collection_style", $inlinkz_collection_id, $inlinkz_collection_style, $inlinkz_addedit); ?></td>
            </tr>
            <tr>
                <td width="40%" align="right">Displayed URL length (for narrow blogs)</td>
                <td><?php echo inlinkz_select("collection_url_length", $inlinkz_collection_id, $inlinkz_collection_url_length, $inlinkz_addedit); ?></td>
            </tr>
            <tr>
                <td width="40%" align="right">Allowed chars in name field</td>
                <td><?php echo inlinkz_select("collection_name_length", $inlinkz_collection_id, $inlinkz_collection_name_length, $inlinkz_addedit); ?></td>
            </tr>
        </table>    
        <table class="widefat">
            <tbody>
                <tr>
                    <td class="row-title"><label for="tablecell"></label>Notify</td>
                    <td></td>
                </tr>
            </tbody>
        </table>  
        <table id="inlinkz_add_collection" class="form_table" width="100%" bgcolor="#EDF2FE">
            <tr>
                <td width="40%" align="right">Review links before publish </td>
                <td><?php echo inlinkz_review_links($inlinkz_collection_id, $inlinkz_addedit, $inlinkz_collection_review_links); ?></td></td>
            </tr>
            <tr>
                <td width="40%" align="right">Email me at </td>
                <td><input type="text" name="collection_email" style="width: 300px"
                    <?php echo('value="' . $inlinkz_collection_email . '"'); ?>/> on link insertion 
                    <?php echo inlinkz_notify($inlinkz_collection_id, $inlinkz_addedit, $inlinkz_collection_notify); ?></td>
            </tr>
            <tr>
                <td colspan="2" align="center" valign="middle">
                    <p class="submit"><?php echo inlinkz_addoredit($inlinkz_collection_id, $inlinkz_addedit); ?></p>
                </td>
            </tr>
        </table>    
    </form>
<?php
}

//gets the data of collection from its id. Data_to_take can be either name, start, end, columns, all
function inlinkz_getCollectionDataFromId($inlinkz_collection_id, $inlinkz_data_to_take) 
{
    global $wpdb, $inlinkz_colDBTbl;

    $inlinkz_query = "SELECT * FROM $inlinkz_colDBTbl WHERE id ='$inlinkz_collection_id'";
    $inlinkz_collectionData = $wpdb->get_row($inlinkz_query);
    if (strcmp($inlinkz_data_to_take, "name") == 0) {
        return $inlinkz_collectionData->collection_name;
    } else if (strcmp($inlinkz_data_to_take, "start") == 0) {
        return $inlinkz_collectionData->collection_start;
    } else if (strcmp($inlinkz_data_to_take, "end") == 0) {
        return $inlinkz_collectionData->collection_end;
    } else if (strcmp($inlinkz_data_to_take, "columns") == 0) {
        return $inlinkz_collectionData->collection_columns;
    } else if (strcmp($inlinkz_data_to_take, "style") == 0) {
        return $inlinkz_collectionData->collection_style;
    } else if (strcmp($inlinkz_data_to_take, "url_length") == 0) {
        return $inlinkz_collectionData->collection_url_length;
    } else if (strcmp($inlinkz_data_to_take, "name_length") == 0) {
        return $inlinkz_collectionData->collection_name_length;
    } else if (strcmp($inlinkz_data_to_take, "email") == 0) {
        return $inlinkz_collectionData->collection_email;
    } else if (strcmp($inlinkz_data_to_take, "notify") == 0) {
        return $inlinkz_collectionData->collection_notify;
    }else if (strcmp($inlinkz_data_to_take, "review") == 0) {
        return $inlinkz_collectionData->collection_review_links;
    }else if (strcmp($inlinkz_data_to_take, "all") == 0) {
        return $inlinkz_collectionData;
    } else {
        echo ('wrong use of getCollectionDataFromId()\n');
        return NULL;
    }
    echo ('wrong use of getCollectionDataFromId()\n');
    return NULL;
}

function inlinkz_review_links($inlinkz_collection_id, $inlinkz_addedit,$inlinkz_review_links ){
    if ($inlinkz_addedit == 1) {
        $inlinkz_review_links = "on";
    } else if ($inlinkz_addedit == 2) {
        $inlinkz_review_links = inlinkz_getCollectionDataFromId($inlinkz_collection_id, "review");
    }
    $inlinkz_ret = "<input type='checkbox' name='collection_review_links'";
    if (strcmp($inlinkz_review_links, "on") == 0) {
        $inlinkz_ret .= " value='on' checked />";
    } else if (strcmp($inlinkz_review_links, "") == 0) {
        $inlinkz_ret .= " value='on' />";
    }
    return $inlinkz_ret;
}

function inlinkz_addoredit($inlinkz_collection_id, $inlinkz_addedit) {
    if ($inlinkz_addedit == 1 || $inlinkz_addedit == 4) {
        $inlinkz_ret = "<input name='inlinkz_add_collection' type=submit value='Add collection' />";
        $inlinkz_ret .= "<input type=hidden name=page value=inlinkz-linkup.php />";
        $inlinkz_ret .= "<input type=hidden id=commandInput name=command value=add_collection />";
    } else if ($inlinkz_addedit == 2 || $inlinkz_addedit == 3)  {
        $inlinkz_ret = "<input name='inlinkz_add_collection' type=submit value='Edit collection' />";
        $inlinkz_ret .= "<input type=hidden name=page value=inlinkz-linkup.php />";
        $inlinkz_ret .= "<input type=hidden id=commandInput name=command value=collection_edited />";
        $inlinkz_ret .= "<input type=hidden name=collection_id value=" . $inlinkz_collection_id . "/>";
    }
    $inlinkz_ret .= "<button class=button onClick=\"document.getElementById('commandInput').value='';\" >Cancel</button>";
    return $inlinkz_ret;
}

function inlinkz_add_collection($inlinkz_collection_name, $inlinkz_collection_start, 
        $inlinkz_collection_end, $inlinkz_collection_columns, $inlinkz_collection_style,
        $inlinkz_collection_url_length, $inlinkz_collection_name_length, $inlinkz_collection_email,
        $inlinkz_collection_notify, $inlinkz_collection_review_links) 
{
    global $wpdb, $inlinkz_colDBTbl;

    $inlinkz_dummy_link_id = 0;
    $inlinkz_result = inlinkz_checkDublication("collections", $inlinkz_collection_id, $inlinkz_collection_name, $inlinkz_dummy_link_id);
    if ($inlinkz_result == -1) {
        return $inlinkz_result;
    }

    $inlinkz_query = "INSERT INTO " . $inlinkz_colDBTbl .
            " (collection_name, collection_start, collection_end, collection_columns, collection_style,
                      collection_url_length, collection_name_length, collection_email, collection_notify, 
                      collection_review_links )
              VALUES ('" . $inlinkz_collection_name . "','" . $inlinkz_collection_start . "','"
            . $inlinkz_collection_end . "','" . $inlinkz_collection_columns . "','"
            . $inlinkz_collection_style . "','" . $inlinkz_collection_url_length . "','"
            . $inlinkz_collection_name_length . "','" . $inlinkz_collection_email . "','"
            . $inlinkz_collection_notify . "','" . $inlinkz_collection_review_links . "');";

    //ceho $inlinkz_query;
    $wpdb->query($inlinkz_query);
    $inlinkz_collection_id = $wpdb->insert_id;
    return $inlinkz_collection_id;
}

function inlinkz_delete_collection($inlinkz_collection_id) {
    global $wpdb, $inlinkz_colDBTbl;

    $inlinkz_delete_query = "DELETE FROM " . $inlinkz_colDBTbl . " WHERE id=" . $inlinkz_collection_id . ";";
    $wpdb->query($inlinkz_delete_query);
}

//user select collection
function inlinkz_selectCollection() {
?>    
    <script type="text/javascript">
        jQuery('button.collectionSelectionButton').click(function(){
            var win = window.dialogArguments || opener || parent || top;
            win.resizeTo(1000,1000);
            var did = jQuery(this).attr('collectionId');
            win.send_to_editor('[inlinkz id = ' + did + ']');
        });
    </script>
<?php
    $inlinkz_collections = inlinkz_get_collections();
?>
    <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="50%">
        <thead>
            <tr>
                <th scope="col" width="5%"></th>
                <th scope="col" width="40%"><div align="center"><h2>Select the collection</h2></th></div>
                <th scope="col" width="5%"></th>
        </tr>
    </thead>
    </table>
<?php
    foreach ($inlinkz_collections as $inlinkz_collection) {
?>
       <table class="widefat fixed" cellspacing="0" id="inlinkz_manage_collections" width="50%">
           <tr class="alternate">
               <td align="right"><?php echo($inlinkz_collection->collection_name); ?></td>
               <td align="left"><button id=collection_<?php echo($inlinkz_collection->id); ?> collectionId=<?php echo($inlinkz_collection->id); ?> class=collectionSelectionButton>Insert</button></td>
           </tr>
       </table>
<?php
    }
}
?>