<?php


function type_info_retrieve_table($args){
    $subject_element_number = 51; #Type
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return table_double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}


function table_double_field_info($args){
    if ($args){
        $taletype_search_url = url(array('module'=>'items','controller'=>'browse'), 'default', 
                                array("search" => "",
                                    "submit_search" => "Zoeken",
                                    "advanced[0][element_id]" => "49",
                                    "advanced[0][type]" => "is exactly",
                                    "advanced[0][terms]" => "$args"));
        $tale_view_url = get_element_by_value($args, "Identifier") ? record_url(get_element_by_value($args, "Identifier"), 'show') : "";
        $type_information = __("Temporarily no title information"); ## REPLACE BY THIS:  $type_information = get_type_description($args);
        $pasted_args = str_replace(array(" ", "\r"), "", $args);
        return browse_link_in_table($value, $search_url, $additional_information);
   }
   return __("something went wrong");
}

function present_dates_as_language_admin($args){
    $printable = new DateFormatHuman($args);
    return $printable->formatHuman() . " (" . $args . ")";
}

/**
* contributor_information_tab_admin
*   Adds a div with user information concerning the item
**/
function contributor_information_tab_admin($args){
    $item = $args['item'];
    $recordId = $item->id;

    $owner = get_db()->getTable('User')->find(metadata($item, "owner_id"))->name;

    echo "<div class='panel'>";
    
    echo "<h4>" . __("Contributor information") . "</h4><br>";

    echo "<p><b>added</b>: " . metadata($item, "added") . "</p>";
    echo "<p><b>by</b>: " . $owner . "</p>";
    echo "<p><b>modif.</b>: " . metadata($item, "modified") . "</p>";
    
    echo "</div>";
}


function all_items_with_this_subject($args){
    if (metadata("item", 'Item Type Name') == "Volksverhaaltype"){
        $search_url = url(array('module'=>'items','controller'=>'browse'), 
                        'default', 
                        array("search" => "",
                            "submit_search" => "Zoeken",
                            "advanced[0][element_id]" => "49",
                            "advanced[0][type]" => "is exactly",
                            "advanced[0][terms]" => "$args",
                            )
                        );
        return "$args <a class='small blue advanced-search-link button' href='$search_url'>alle items van dit type</a>";
    }
    return $args;
}


function my_type_link_function_admin($args){
    if ($args){
        $type_information = get_type_info($args);
        $search_url = url(array('module'=>'items','controller'=>'browse'), 
                        'default', 
                        array("search" => "",
                            "submit_search" => "Zoeken",
                            "advanced[0][element_id]" => "49",
                            "advanced[0][type]" => "is exactly",
                            "advanced[0][terms]" => "$args",
                            )
                        );
        return "link";
        return "<a class='hover-type' href='$search_url'>$args</a> <span> - $type_information</span>";
    }
	return "No value";
}
?>