<?php
function checkImagesAddOn(){
    if (get_option("inlinkz_imagesAddOn") === false){
        add_option("inlinkz_imagesAddOn", "false");
    }
}
?>