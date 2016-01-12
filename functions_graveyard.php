<?php

function get_type_info($search_string)
{
	$db = get_db();
	$sql = "
	SELECT items.id 
	FROM {$db->Item} items 
	JOIN {$db->ElementText} element_texts 
	ON items.id = element_texts.record_id 
	JOIN {$db->Element} elements 
	ON element_texts.element_id = elements.id 
	JOIN {$db->ElementSet} element_sets 
	ON elements.element_set_id = element_sets.id 
	WHERE element_sets.name = 'Dublin Core' 
	AND elements.name = 'Identifier' 
	AND element_texts.text = ?";
	$itemIds = $db->fetchAll($sql, $search_string);
#	print_r(get_class_methods($itemIds));
	if (count($itemIds) >= 0){ //NOG EVEN MEE VERDER STOEIEN
		$temp_item = "";
		$found_item = get_record_by_id('item', $itemIds[0]["id"]);
		$temp_return = metadata($found_item, array('Dublin Core', 'Title')) . "<br>";
		$temp_return .= metadata($found_item, array('Dublin Core', 'Creator')) . "<br>";
		$temp_return .= metadata($found_item, array('Dublin Core', 'Publisher')) . "<br>";
		$temp_return .= metadata($found_item, array('Item Type Metadata', 'Subgenre')) . "<br>";
		return $temp_return;
	}
	return "no description";
}


function subject_info_retrieve_popup($args){
    if ($args){
        $tale_search_url = url(array('module'=>'items','controller'=>'browse'), 'default', 
                        array("search" => "",
                            "submit_search" => "Zoeken",
                            "advanced[0][element_id]" => "49",
                            "advanced[0][type]" => "is exactly",
                            "advanced[0][terms]" => "$args",
                            )
                        );
        $html =  '<!-- popup form #1 -->';
        $html .= '<a href="#login_form" id="login_pop">'.$args.'</a>';
        $html .= '  <div class="popup">';
        $html .= '      <h2>'.$args.'</h2>';
        $html .= '      <a href="'.$tale_search_url.'">Alle verhalen van het type '.$args.'</a>';
        $html .= '      <a class="close" href="#close"></a>';
        $html .= '  </div>';
        $blerg = '
        <div class="main">
            <div class="panel">
                
                <a href="#'.$args.'" id="browse_button">'.$args.'</a> Extra info
            </div>
        </div>

        <!-- popup form #1 -->
        <a href="#x" class="overlay" id="'.$args.'"></a>
        <div class="popup">
            <h2>'.$args.'</h2>
            <a href="'.$tale_search_url.'">Alle verhalen van het type '.$args.'</a>
            <a class="close" href="#close"></a>
        </div>';
        return $blerg;
    }
}


function my_type_link_function_public($args){
    if ($args){
#        $type_information = get_type_info($args);
        $type_information = "TEMP";
        $search_url = url(array('module'=>'items','controller'=>'browse'), 
                        'default', 
                        array("search" => "",
                            "submit_search" => "Zoeken",
                            "advanced[0][element_id]" => "49",
                            "advanced[0][type]" => "is exactly",
                            "advanced[0][terms]" => "$args",
                            )
                        );
        $return_this = "<a class='hover-type' href='$search_url'>$args <span><center>$type_information</center></span></a>";
        return $return_this;
    }
	else{
		return false;
	}
}

function get_type_description_old($search_string){
    _log("2 BEFORE GET_DB: ");
    $db = get_db();
    $sql = "
    SELECT items.id 
    FROM {$db->Item} items 
    JOIN {$db->ElementText} element_texts 
    ON items.id = element_texts.record_id 
    JOIN {$db->Element} elements 
    ON element_texts.element_id = elements.id 
    JOIN {$db->ElementSet} element_sets 
    ON elements.element_set_id = element_sets.id 
    WHERE element_sets.name = 'Dublin Core' 
    AND elements.name = 'Identifier' 
    AND element_texts.text = ?";
    _log("3 BEFORE FETCHING: ");
    $itemIds = $db->fetchAll($sql, $search_string);
#    $itemIds = array("1000");
    _log("4 AFTER FETCHING: ");
    if (count($itemIds) > 0){ //NOG EVEN MEE VERDER STOEIEN
        $found_item = get_record_by_id('item', $itemIds[0]["id"]);
#        print_pre($found_item);
#        metadata($found_item, array('Dublin Core', 'Title'));
        $temp_return = metadata($found_item, array('Dublin Core', 'Title')) . " - " . metadata($found_item, array('Dublin Core', 'Description'), array("snippet" => 140));
#        print_pre($temp_return);
        return $temp_return;
        return "DOES exist";
    }
    return "Type doesn't exist";
}

?>