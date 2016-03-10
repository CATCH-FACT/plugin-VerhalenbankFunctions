<?php

/*
*   Code for the representation of motifs on the public site.
*           
*/
function motif_info_retrieve_popup_jquery($args){
    $subject_element_number = 52;           #Subject  
    $search_element = "Identifier";         #Here, the value of the metadata is looked up in identifier
    $return_element = "Title";              #The title of the found Item is returned in the HTML code
    $return_itemset = "Dublin Core";
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args, $return_itemset, get_option('motiflink'));
}

/*
*   Code for the representation of kloeke nummers.
*           
*/
function kloeke_info_retrieve_popup_jquery($args){
    $subject_element_number = 69;
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args, null, get_option('kloekelink'));
}

function pvh_info_retrieve_popup_jquery($args){
    $subject_element_number = 65; //place of action
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function nep_info_retrieve_popup_jquery($args){
    $subject_element_number = 93; //Named entity place zoeken
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function nea_info_retrieve_popup_jquery($args){
    $subject_element_number = 66; //Named entity actor zoeken
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function ne_other_info_retrieve_popup_jquery($args){
    $subject_element_number = 63; //Named entity zoeken
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function title_maker_info_retrieve_popup_jquery($args){
    $subject_element_number = 39; //Maker zoeken
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function title_collector_info_retrieve_popup_jquery($args){
    $subject_element_number = 60; //Maker zoeken
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function identifier_info_retrieve_popup_jquery($args){
    $subject_element_number = 49; //subject zoeken
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function type_info_retrieve_popup_jquery($args){
    $subject_element_number = 51; #Type
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function subgenre_info_retrieve_popup_jquery($args){
    $subject_element_number = 58; #Subgenre
    $search_element = null;
    $return_element = null;
    $collection = 1;
    $return_itemset = "Item Type Metadata";
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function subject_info_retrieve_popup_jquery($args){
    $subject_element_number = 49;           #Subject  
    $search_element = "Identifier";         #Here, the value of the metadata is looked up in identifier
    $return_element = "Title";              #The title of the found Item is returned in the HTML code
    $collection = 1;                        #collection 1 is used to link to
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

/*function motif_info_retrieve_popup_jquery($args){
    $subject_element_number = 52;           #Subject  
    $search_element = "Identifier";         #Here, the value of the metadata is looked up in identifier
    $return_element = "Title";              #The title of the found Item is returned in the HTML code
    $collection = 1;                        #collection 1 is used to link to
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}*/

/*
*   returns html code for aditional link information on the public or admin site
*   @arguments:
*   
* @param Object $args       The Item object
* @return string            html code
*
* More settings: 
*/
function creator_info_retrieve_popup_jquery($args){
    $subject_element_number = 39; #Creator / verteller
    $search_element = null;
    $return_element = null;
    $collection = null;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function language_info_retrieve_popup_jquery($args){
    $subject_element_number = 44; #Language
    $search_element = null;
    $return_element = null;
    $collection = 1;
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args);
}

function collector_info_retrieve_popup_jquery($args){
    $subject_element_number = 60; #Collector
    $search_element = null;
    $return_element = null;
    $collection = 1;
    $return_itemset = "Item Type Metadata";
    return double_field_info($subject_element_number, $search_element, $return_element, $collection, $args, $return_itemset);
}

function present_dates_as_language($args){
    $printable = new DateFormatHuman($args);
    return strtolower($printable->formatHuman());
}

/*
*   Returns HTML containing links to connected internal data sources
*   $subject_element_number = the element ID that needs linking
*   $search_element = The element (element title!) that needs to be found for linking
*   $return_element = The element (element title!) that needs to be shown in addition to the subject element
*   $collection = The collection that needs to be looked in
*   $original_value = The original subjects value (for showing)
*   $return_itemset = The Itemset that the return element is in (Dublin Core or Item Type Metadata)
*   $external_link = An external link resource after which a value will be pasted
*   it figures out if the same ID is present in the Lexicon, Perrault, Grimm, or Vertellers records.
*/
function double_field_info($subject_element_number, $search_element = null, 
                                $return_element = null, $collection = null, 
                                $original_value = null, $return_itemset = 'Dublin Core',
                                $external_link = null){
    $links = array();
    $supplemented_value = $original_value;
    $additional_information = null;
    if (!empty($original_value) && $search_element && $return_element){
        $additional = get_element_by_value($original_value, $search_element);
        if ($additional) { 
            $additional_information_pre = $additional->getElementTexts($return_itemset, $return_element);
            $additional_information = $additional_information_pre[0]["text"];
        }
    }
    if ($external_link){
        $links[] = "<a href='$external_link$original_value' target='motif'>$original_value: " . __("External link") . "</a><br>";
    }
    $links[] = info_search_link($subject_element_number, $original_value, $collection, __("All folktales"));
//    $links[] = info_search_link(43, $original_value, 3, __("All folktale types"));      //43 is Identifier
    $links[] = info_item_link($search_element, $original_value, 3, "verhaaltype");  //check if the link to the item can be found
    $links[] = info_item_link("Subject", $original_value, 2, "in Lexicon");         //check if the value can be found in subcollection Lexicon
    $links[] = info_item_link("Subject", $original_value, 6, "in Perrault");        //check if the value can be found in subcollection Perrault
    $links[] = info_item_link("Subject", $original_value, 7, "in Grimm");           //check if the value can be found in subcollection Grimm
    $links[] = info_item_link("Title", $original_value, 4,   "in Vertellers");      //check if the value can be found in subcollection Vertellers
    $links[] = info_item_link("Title", $original_value, 9,   "in Verzamelaars");      //check if the value can be found in subcollection Verzamelaars
    if (is_admin_theme()) {
        return browse_link_in_table($original_value, $additional_information, $links); //the additional information is put in a table format
    }
    else{
        return browse_link_in_toggler($original_value, $additional_information, $links);   // the additional information is put in a jquery toggler
    }
}

function browse_link_in_menu($original_value, $additional_information, $links){
    $html = $original_value;
    $supplemented_value = $original_value . ($additional_information ? " - " . $additional_information : "");
    if ($supplemented_value){
        $allowed = "/[^a-z0-9]/i";
        $pasted_args = preg_replace($allowed, "", $original_value); //for unique id name
        $html = '
        <div id="button">
            <ul class="hover">
               <li class="hoverli">
                   <p>' . $supplemented_value . '</p>
                    <ul class="file_menu">';
        foreach ($links as $link){
            if ($link){$html .= '<li>'.$link.'</li>';}
        }
        $html .= '  </ul>
                <li>
            </ul>
        </div>';
    }
    return $html;
}


function browse_link_in_toggler($original_value, $additional_information, $links){
    $html = $original_value;
    $supplemented_value = $original_value . ($additional_information ? " - <i>" . $additional_information . "</i>" : "");
    
    if ($supplemented_value && !preg_match('/<.+>/', $original_value)){
        $allowed = "/[^a-z0-9]/i";
        $pasted_args = preg_replace($allowed, "", $original_value); //for unique id name
        $html = '<p class="toggler" id="toggler-' . $pasted_args . '" style="display: inline;">
                <span class="expandSlider">' . $supplemented_value . ' &nbsp&nbsp <img src= "' . url("themes/verhalenbank/images/down.gif").'"></span>
                <span class="collapseSlider">' . $supplemented_value . ' &nbsp&nbsp <img src= "'   . url("themes/verhalenbank/images/up.gif").'"></span>
            </p>
            <div class="slider" id="'.$pasted_args.'">';
        foreach ($links as $link){ $html .= $link;}
        $html .= '</div>';
    }
    return $html;
}

function browse_link_in_table($original_value, $additional_information, $links){
    $html = $original_value . '<table>';
    if ($additional_information){
        $html .= 
        '    <tr>
                <th>' . 
                    $additional_information . '<br />
                </th>
            </tr>';
    }
        $html .= '<tr>
            <td>';
    foreach ($links as $link){ $html .= $link;}
    $html .= '</td></tr>';
        $html .= '</table>';
    return $html;
}


function info_item_link($element_name, $search_term, $collection_id = NULL, $ga_naar_text = ""){
    if (get_element_by_value($search_term, $element_name, $collection_id)){
//        _log("info_item_link: " . $element_name . $search_term . " " . $collection_id . $ga_naar_text, $priority = Zend_Log::DEBUG);
        $url = record_url(get_element_by_value($search_term, $element_name, $collection_id), 'show');
        return '<a href='.$url.'>' . $search_term . ' '  . $ga_naar_text . '</a><br>';
    }
    return "";
}


/*
*   Returns code for scrolling to the full text of the item
*
*/
function scroll_to_full_text($args){
//    $itemtype = "volksverhaal";
//    return $args . "<br><b><a id='text-scroll' href='#$itemtype-item-type-metadata-text'>" . __("View full text") . "</a></b>";
}


/*
*   returns links to an items in subcollections containing this Id
*   @arguments:
*   
* @param int $element_number     The element number to be searched in
* @param int $search_term       The term that should be searched for
* @return string                Links to items in subcollections
*/
function info_subcollection_items($element_number, $search_term, $collections = array(2,6,7)){
    $element = get_db()->getTable("Element")->find($element_number);
    $element_name = $element->name;
    $links = "";
    foreach($collections as $collection){
        $taletype_search_url = url(array('module'=>'items','controller'=>'browse'), 'default', 
                                array("search" => "",
                                    "submit_search" => "Zoeken",
                                    "collection" => $collection,
                                    "advanced[0][element_id]" => "$element_number",
                                    "advanced[0][type]" => "is exactly",
                                    "advanced[0][terms]" => "$search_term"));
        $links .= "<a href='".$taletype_search_url."'> " . __($element_name) . get_record_by_id('Collection', $collection)->name . ": " . $search_term . "</a><br>";
    }
    return $links;
}

/*
*   returns hidden HTML with links to an item and to a list of items containing this Id
*   @arguments:
*   
* @param int $element_number     The element number that should be searched
* @param int $search_term       The term that should be searched
* @return string                Link to a search
*/
function info_search_link($element_number, $search_term, $collection = 1, $commentary){
    $element = get_db()->getTable("Element")->find($element_number);
    $element_name = $element->name;
    
    $search_url = url(array('module'=>'solr-search', 'controller'=>'index'), 'default',
        array("facet" => $element_number . "_s:\"" . $search_term. "\""));
    
/*    $search_url = url(array('module'=>'items','controller'=>'browse'), 'default', 
                            array("search" => "",
                                "submit_search" => "Zoeken",
                                "collection" => $collection,
                                "advanced[0][element_id]" => "$element_number",
                                "advanced[0][type]" => "is exactly",
                                "advanced[0][terms]" => "$search_term"));*/
    return "<a href='" . $search_url . "'>" . $search_term . ": " . $commentary . "</a><br>";
}

/*
*       this returns a warning that the text contains copyrighted information
*
*/
function text_copyright_hide($args){
    if ($user = current_user()){
        return $args;
    }
    if ($args){
        if (metadata(get_current_record('item'), array('Dublin Core', 'Rights')) == "nee"){
            return get_option('textcopyrightwarning');
        }
        else{
            return $args;
        }
    }
    else{
        return false;
    }
}

/*
*       this returns a warning that the text might contain extreme elements.
*/
function text_extreme_hide($args){
	if ($user = current_user()){
		return $args;
	}
	if ($args){
		if (metadata(get_current_record('item'), array('Item Type Metadata', 'Extreme')) == "ja"){
			return __(get_option('textextremewarning'));
		}
		else{
			return $args;
		}
	}
	else{
		return false;
	}
}

/*
*   This returns a warning that the user is private when specified in the user's record.
*/
function creator_privacy_hide($args){
	if ($user = current_user()){
		return $args;
	}
    return __(get_option('creatorprivatewarning'));
/*	if ($args){
		if (!get_elements_private_status_by_value($args)){
			return get_option('creatorprivatewarning');
		}
		else{
			return $args;
		}
#		return info_item_link("Title", "", 4,   "in Vertellers");
	}
	else{
		return false;
	}*/
}

?>