<?php
if (!defined('VERHALENBANKFUNCTIONS_PLUGIN_DIR')) {
    define('VERHALENBANKFUNCTIONS_PLUGIN_DIR', dirname(__FILE__));
}
if (!defined('VERHALENBANKFUNCTIONS_IMAGE_DIR')) {
    define('VERHALENBANKFUNCTIONS_IMAGE_DIR', dirname(__FILE__) . "/views/shared/images");
}

require_once VERHALENBANKFUNCTIONS_PLUGIN_DIR . '/helpers/ElementFunctions.php';
require_once VERHALENBANKFUNCTIONS_PLUGIN_DIR . '/helpers/admin_functions.php';
require_once VERHALENBANKFUNCTIONS_PLUGIN_DIR . '/helpers/public_functions.php';
require_once VERHALENBANKFUNCTIONS_PLUGIN_DIR . '/helpers/DateConversions.php';

class VerhalenbankFunctionsPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_hooks = array('install',
	                            'public_head',
                                'config_form',
                                'config',
                                'public_items_show',
                                'public_items_show_sidebar_top',
//                                'public_items_show_sidebar_ultimate_top',
                                'admin_head',
                                'admin_items_show_sidebar',
                                'initialize',
                                'items_browse_sql',
                                'define_acl',);
	
	protected $_filters = array('display_elements',
	                            'file_markup',
	                            'item_citation',
	                            'public_navigation_admin_bar',
                                'admin_dashboard_panels',
                                'admin_dashboard_stats',
                                'public_navigation_items',
                                'media_rss_action_context'
                                );

//    public $_metadata_public_hide = array("Dublin Core" => array("Contributor", "Rights"),
//                                            "Item Type Metadata" => array("Extreme", "Kloeke Georeference", "Entry date", "Corpus"));

    public $_metadata_public_hide = array( array("Dublin Core", "Contributor"), 
                                            array("Dublin Core", "Rights"),
                                            array("Dublin Core", "Title"),
                                            array("Item Type Metadata", "Extreme"),
                                            array("Item Type Metadata", "Kloeke Georeference"), 
                                            array("Item Type Metadata", "Entry date"),
                                            array("Item Type Metadata",  "Corpus"));

    public $_metadata_to_the_right = array( array("Dublin Core", "Identifier"),
                                            array("Item Type Metadata", "Subgenre"),
                                            array("Dublin Core", "Type"),
                                            array("Dublin Core", "Language"),
                                            array("Dublin Core", "Date"),
                                            array("Dublin Core", "Coverage"),
                                            array("Item Type Metadata", "Kloeke Georeference"),
                                            array("Dublin Core", "Creator"),
                                            array("Dublin Core", "Contributor"),
                                            array("Item Type Metadata", "Collector"),
                                            array("Dublin Core", "Rights"),
                                            array("Item Type Metadata", "Literary"),
                                            array("Item Type Metadata", "Extreme"));
    
    function filterMediaRssActionContext($context)
    {
        $context['rssm'] = array('suffix'  => 'rssm', 
                                'headers' => array('Content-Type' => 'text/xml'));

        return $context;
    }

    /**
     * Return HTML props
     * 
     * @return string
     */
    public function link_to_search_arguments($advanced_style){
        $uri = "items/search?style=$advanced_style";
        $props = $uri . (!empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '');
        return $props;
    }

    public function filterPublicNavigationItems($navArray){
        unset($navArray[0]); #unsetting browse all items
        unset($navArray[2]); #unsetting start search from scratch
        $navArray['blorptest'] = array('label'=>__('Modify search'),
                                       'uri' => url($this->link_to_search_arguments("advanced"))
                                       );
        return $navArray;
    }

    public function hookConfigForm()
    {
        // If necessary, upgrade the plugin options
        include 'config_form.php';        
    }

    public function hookConfig($args)
    {
        $post = $args['post'];
        set_option('textcopyrightwarning', $post['textcopyrightwarning']);
        set_option('textextremewarning', $post['textextremewarning']);
        set_option('creatorprivatewarning', $post['creatorprivatewarning']);
        set_option('imagewarning', $post['imagewarning']);
        set_option('kloekelink', $post['kloekelink']);
        set_option('motiflink', $post['motiflink']);
        set_option('subcollectionswithtypes', $post['subcollectionswithtypes']);
        set_option('mediumsearchablefields', $post['mediumsearchablefields']);
        set_option('mediumsearchstyle', $post['mediumsearchstyle']);
    }
    
    public function hookInstall(){
        set_option('creatorprivatewarning', 
            "<p style = 'color:red'><b>Verteller privé</b></p>");
            
        set_option('textcopyrightwarning', 
            "<p style = 'color:red'><b>Auteursrecht:</b></p> 
De tekst bevat auteursrechtelijk beschermde informatie. 
De inhoud is daarom afgeschermd, en kan alleen worden geraadpleegd op het Meertens Instituut.
<br>
This text contains copyrighted information.");

        set_option('textextremewarning', 
            "<p style = 'color:red'><b>Extreme:</b></p> 
Dit record bevat extreme elementen van enigerlei aard (racisme, sexisme, schuttingtaal, godslastering, expliciete naaktheid, majesteitsschennis). 
De inhoud is daarom afgeschermd, en kan alleen worden geraadpleegd op het Meertens Instituut.
<br>
This text contains language that can be perceived as extreme.");

        set_option('imagewarning', 
            "<p style = 'color:red'>Waarschuwing: </p>Dit bestand bevat auteursrechtelijke informatie of bevat extreme facetten. <br>
De inhoud is daarom afgeschermd, en kan alleen worden geraadpleegd op het Meertens Instituut, of met een admin account.");

        set_option('kloekelink', 'http://www.meertens.knaw.nl/kaart/v3/rest/?type=dutchlanguagearea&data[]=');
        set_option('motiflink', 'http://www.dinor.demon.nl/motif/index.html?');
        set_option('subcollectionswithtypes', "2,6,7");
        set_option('mediumsearchablefields', "43,49,50,60,44,48,39,40,61,58,52,41,63,66,93,65,53,67,51,1");
        set_option('mediumsearchstyle', "contains");
    }

    /**
     * Initialize the plugin.
     */
    public function hookInitialize(){
        
        // adding shortcodes for SimplePages
        add_shortcode('total_folktales', array($this, 'totalfolktales'));
        
#        queue_css_url("http://www.meertens.knaw.nl/cms/templates/mi_hetgelaat/js/meertens.databanken.js");
        add_translation_source(dirname(__FILE__) . '/languages');
        // Register the select filter controller plugin.
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new InputFieldFilter);
    }

    public function totalfolktales($args, $view){
            $collection = get_record_by_id('Collection', 1);
            return metadata($collection, 'total_items');
//            return 'This is a very simple shortcode.';
        }

    /**
     * Define the ACL.
     * Here some new user rights are set
     *
     * @param Omeka_Acl
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];
        $indexResource = new Zend_Acl_Resource('FolktaleAnnotator_Index');
        $acl->add($indexResource);

        $acl->allow("contributor", "Items", array('makePublic', "edit"));

        $acl->deny("admin", "Collections", 'delete');

        $acl->allow("admin", array("Users"));
    }

	/**
	*   admin_items_show_sidebar
	*   information about the contributor is added on the botton of the sidebar panel
	**/
    public function hookAdminItemsShowSidebar($args){
        contributor_information_tab_admin($args);
	}


    public function filterItemCitation($citation, $args) {
        $citation = "None";
        return $citation;
    }

    public function hookItemsBrowseSql($args){
#        _log("ALSO THE BASIC SEARCH CAN BE EXTENDED HERE", $priority = Zend_Log::DEBUG);
        if(isset($args['params']['keywordsearch'])) {
            $terms = $args['params']['keywordsearch'];
            $db = $this->_db;
            $select = $args['select'];
            $advancedIndex = 0;
            foreach ($terms as $v) {
                // Do not search on blank rows.
                if (empty($v['element_id']) || empty($v['terms'])) {
                    continue;
                }
                $value = $v['terms'];
                $elementId = (int) $v['element_id'];

                $inner = true;
                // Determine what the WHERE clause should look like.
                $alias = "_keywordsearch_{$advancedIndex}";

                // Note that $elementId was earlier forced to int, so manual quoting unnecessary here
                $joinCondition = "{$alias}.record_id = items.id AND {$alias}.record_type = 'Item' AND {$alias}.element_id = $elementId";
                if ($inner) {
                    $select->joinInner(array($alias => $db->ElementText), $joinCondition, array());
                } else {
                    $select->joinLeft(array($alias => $db->ElementText), $joinCondition, array());
                }
                $terms = preg_split("/[\s]*[ ,\.][\s]*/", $value);
#                print "<pre>" . $terms . " - " . $value . "</pre>";
                foreach ($terms as $value) {
                    $predicate = "LIKE " . $db->quote('%'.$value .'%');
                    $select->where("{$alias}.text {$predicate}");
                }
                $advancedIndex++;
            }
        }
    }

    /**
    *   filterDisplayElements:
    *   Here we filter the elements based on the variables $_metadata_public_hide and $_metadata_to_the_right
    *   
    **/
    public function filterDisplayElements($elementSets) {
        if (!is_admin_theme()) { #only in the public view!
            //remove text from view because it is explicitly put there in the php
            unset($elementSets["Volksverhaal Item Type Metadata"]["Text"]);
            //here we take out the elements that will be shown on the div on the metadata div
            foreach($this->_metadata_to_the_right as $set_and_element){
                if ($set_and_element[0] == "Item Type Metadata"){
                    unset($elementSets["Volksverhaal " . $set_and_element[0]][$set_and_element[1]]);
                }
                else{
                    unset($elementSets[$set_and_element[0]][$set_and_element[1]]);
                }
            }
            if ($user = current_user()){ #don't filter this stuff out when logged in
                return $elementSets;
            }
            foreach($this->_metadata_public_hide as $set_and_element){ #omitting the element set names
                if ($set_and_element[0] == "Item Type Metadata"){
                    unset($elementSets["Volksverhaal " . $set_and_element[0]][$set_and_element[1]]);
                }
                else{
                    unset($elementSets[$set_and_element[0]][$set_and_element[1]]);
                }
            }
            return $elementSets;
        }
        return $elementSets;
    }
    
    public function hookPublicItemsShowSidebarTop($args){
        $item = $args['item'];
        $tdcolor = false;
        $html = "<div class=\"element-set\">";
        $html .= "  <div class=\"element-text\">";
        $html .= "      <table id=\"metadata\">";
        foreach($this->_metadata_to_the_right as $set_and_element){
            if (metadata('item', array($set_and_element[0], $set_and_element[1]), array('all' => true))){ // don't show when empty
                $tdcolor = !$tdcolor;
                $html .= '<tr id="metadata" class="element" style="background-color:' . ($tdcolor ? '#FFCCCC' : 'white') . ';">';
                $html .= '  <td style="color: #ab2929; font-family: Georgia, Times, "Times New Roman", serif; font-size: 1em;">' . __($set_and_element[1]) . ':&nbsp</td>';
                $html .= '  <td>';
                foreach(metadata('item', array($set_and_element[0], $set_and_element[1]), array('all' => true)) as $key => $value){
                    $html .= '' . $value . '<br>';
                }
                $html .= '  <td>';
                $html .= '</tr>';
            }
        }
        $html .= "      </table>";
        $html .= "  </div>"; 
        $html .= "</div>";
        if ($html){
            print '<div id="item-metadata" class="element">';
            print '<h2>Metadata</h2>';
            
            // infolink button for unfolding all metadata click options
            print "<ul class='slide-toggle'>";
            print '<li class="up" id="slidetoggle">'.__("Show browse links").'</li>'; #Informatie uitklappen
            print '<li class="down" id="slidetoggle" style="display:none;">'.__("Hide browse links").'</li>';
            print "</ul>";
            
            print $html;
            print "</div>";
        }
        
        if (metadata("item", 'Item Type Name') == "Persoon"){
            clear_filters(array('Display', 'Item', 'Dublin Core', 'Title'));
            print $this->vertellerVerhalenVerteld(metadata("item", array('Dublin Core', 'Title')));
        }
    }
    
    
    /**
    *   Fetches information about the maker or narrator.
    *   @returns html code containing amount of tales, and search link for narrator's tales.
    *
    **/
    private function vertellerVerhalenVerteld($verteller){
        $maxverhalen = 50;
        $verteldeVerhalen = NULL;
        $amount_tales = __("max. $maxverhalen weergegeven");
        if (metadata("item", 'collection_name') == "Vertellers"){
            $verteldeVerhalen = get_list_elements_by_value($verteller, "Creator", 1, $maxverhalen);
#            $amount_tales = count($verteldeVerhalen);
        }
        if (metadata("item", 'collection_name') == "Verzamelaars"){
            $verteldeVerhalen = get_list_elements_by_value($verteller, "Collector", 1, $maxverhalen);
#            $amount_tales = count_elements($verteller, "Collector", 1, $maxverhalen);
        }
        $tales_link = null;
        $html = '<div id="item-metadata" class="element">';
        $html .= '<h2>' . __(metadata("item", 'collection_name')) . ' ' . __("folktales") . ' (' . $amount_tales . ') </h2>';
        foreach($verteldeVerhalen as $verteldVerhaal){
            $url = record_url($verteldVerhaal, 'show');
            $html .= '<li><a href=' . $url . '>' . metadata($verteldVerhaal, array('Dublin Core', 'Identifier')) . " - " . 
                        metadata($verteldVerhaal, array('Dublin Core', 'Title')) . '</a></li>';
        }
        $html .= "</div>";
        return $html;
    }
    
    public function hookPublicItemsShow($args){
//    public function hookPublicItemsShowSidebarUltimateTop($args){
        print "<ul class='slide-toggle'>";
        print "<li id=\"google_translate_element\"></li>";
#        print '<li class="up" id="slidetoggle">'.__("Show browse links").'</li>'; #TRANSLATE Informatie uitklappen
#        print '<li class="down" id="slidetoggle" style="display:none;">'.__("Hide browse links").'</li>';
        print "</ul>";
        print "<script>
            function googleTranslateElementInit() {
                new google.translate.TranslateElement({
                    pageLanguage: 'nl'
                }, 'google_translate_element');
            }
            </script><script src=\"http://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit\"></script>";
        return "TESTSTRING";
    }
    
    public function filterFileMarkup($html, $args){
        if ($user = current_user()){
    		return $html;
    	}
        $file = $args['file'];
        if((metadata($file, array('Dublin Core', 'Rights')) == 'nee') || (metadata($file, array('Dublin Core', 'Relation')) == 'ja')){
            return get_option('imagewarning'); 
            /*'<p>Een bestand bevat auteursrechtelijke informatie of bevat extreme facetten. 
            <br>De inhoud is daarom afgeschermd, en kan alleen worden geraadpleegd op het Meertens Instituut, of met een admin account.<br><br></p>';*/
        }
        return $html;
    }

    /**
     * Add the data linker navigation link.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array('label' => __('Data linker'), 'uri' => url('data-linker'));
        return $nav;
    }

    /**
     * Add the data linker navigation link.
     */
    public function hookPublicHead($args)
    {
        clear_filters(array('Display', 'Item', 'Dublin Core', 'Title'));
        queue_css_file("print", "print"); // queues a css file to print pages
        queue_css_file('linked'); // assumes plugin has a /views/public/css/linked.css file
        queue_js_file('showHide');
        queue_js_file('search_mod');
        $view = get_view();
        if(isset($view->item)) {
            if (metadata("item", 'collection_name') == "Vertellers"){
                add_filter(array('Display', 'Item', 'Dublin Core', 'Title'),                        'title_maker_info_retrieve_popup_jquery', 7);
            }
            if (metadata("item", 'collection_name') == "Verzamelaars"){
                add_filter(array('Display', 'Item', 'Dublin Core', 'Title'),                        'title_collector_info_retrieve_popup_jquery', 7);
            }
            if (metadata("item", 'Item Type Name') == "Volksverhaaltype"){
                add_filter(array('Display', 'Item', 'Dublin Core', 'Identifier'),                   'identifier_info_retrieve_popup_jquery', 7);
            }
            if (metadata("item", 'Item Type Name') == "Volksverhaal"){
                // PRIVACY
                if ($this->get_elements_private_status_by_value(metadata($view->item, array('Dublin Core', 'Creator')))) { #in case of existing privacy issues
                    add_filter(array('Display', 'Item', 'Dublin Core', 'Creator'),                  'creator_privacy_hide', 1);
                    add_filter(array('Display', 'Item', 'Item Type Metadata', 'Collector'),         'creator_privacy_hide', 1);
                    add_filter(array('Display', 'Item', 'Item Type Metadata', 'Contributor'),       'creator_privacy_hide', 1);
                }
#                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Kloeke georeference'),   'my_kloeke_link_function', 4);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Text'),                  'text_extreme_hide', 5);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Text'),                  'text_copyright_hide', 6);
            }
            #TODO: aangeven wanneer dit moet gebeuren zoals hierboven
            if (metadata("item", 'Item Type Name') == "Volksverhaal" || metadata("item", 'Item Type Name') == "Lexicon item" || metadata("item", 'Item Type Name') == "Text Edition"){
                add_filter(array('Display', 'Item', 'Dublin Core', 'Motif'),                        'motif_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Dublin Core', 'Subject'),                      'subject_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Dublin Core', 'Language'),                     'language_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Dublin Core', 'Type'),                         'type_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Dublin Core', 'Creator'),                      'creator_info_retrieve_popup_jquery', 7);

                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Collector'),             'collector_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Subgenre'),              'subgenre_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Named Entity Location'), 'nep_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Named Entity'),          'ne_other_info_retrieve_popup_jquery', 7); #later veranderen
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Named Entity Actor'),    'nea_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Place of Action'),       'pvh_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Motif'),                 'motif_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Kloeke Georeference'),   'kloeke_info_retrieve_popup_jquery', 7);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Kloeke Georeference in Text'),   'kloeke_info_retrieve_popup_jquery', 7);
                
//                add_filter(array('Display', 'Item', 'Dublin Core', 'Description'),                  'scroll_to_full_text', 5); // should check if there is Text available
                add_filter(array('Display', 'Item', 'Dublin Core', 'Source'),                       'make_urls_clickable_in_text', 6);
                add_filter(array('Display', 'Item', 'Item Type Metadata', 'Literature'),            'make_urls_clickable_in_text');
                add_filter(array('Display', 'Item', 'Dublin Core', 'Date'),                         'present_dates_as_language', 20);
            }
        }
        
        if(isset($view->items)) {
            add_filter(array('Display', 'Item', 'Dublin Core', 'Date'),                         'present_dates_as_language', 20);
        }
            
    }


    public function hookAdminHead($args)
    {
         $view = get_view();
         if(isset($view->item)) {
             if (metadata("item", 'collection_name') == "Vertellers"){
                  add_filter(array('Display', 'Item', 'Dublin Core', 'Title'),                    'title_maker_info_retrieve_popup_jquery', 7);
              }
              if (metadata("item", 'collection_name') == "Verzamelaars"){
                  add_filter(array('Display', 'Item', 'Dublin Core', 'Title'),                    'title_collector_info_retrieve_popup_jquery', 7);
              }
             if (metadata("item", 'Item Type Name') == "Volksverhaaltype"){
                 add_filter(array('Display', 'Item', 'Dublin Core', 'Identifier'),               'identifier_info_retrieve_popup_jquery', 7);
             }
             if (metadata("item", 'Item Type Name') == "Volksverhaal"){
                 //when logged in, no privacy!
//                 if ($this->get_elements_private_status_by_value(metadata($view->item, array('Dublin Core', 'Creator')))) { #in case of existing privacy issues
//                     add_filter(array('Display', 'Item', 'Dublin Core', 'Creator'),                  'creator_privacy_hide', 1);
//                 }
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Kloeke georeference'),   'my_kloeke_link_function', 4);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Text'),                  'text_extreme_hide', 5);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Text'),                  'text_copyright_hide', 6);
             }
             #TODO: aangeven wanneer dit moet gebeuren zoals hierboven
             if (metadata("item", 'Item Type Name') == "Volksverhaal" || metadata("item", 'Item Type Name') == "Lexicon item" || metadata("item", 'Item Type Name') == "Text Edition"){
                 add_filter(array('Display', 'Item', 'Dublin Core', 'Subject'),                      'subject_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Dublin Core', 'Language'),                     'language_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Dublin Core', 'Type'),                         'type_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Dublin Core', 'Creator'),                      'creator_info_retrieve_popup_jquery', 7);

                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Collector'),             'collector_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Subgenre'),              'subgenre_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Named Entity Location'), 'nep_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Named Entity'),          'ne_other_info_retrieve_popup_jquery', 7); #later veranderen
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Named Entity Actor'),    'nea_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Place of Action'),       'pvh_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Motif'),                 'motif_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Kloeke Georeference'),   'kloeke_info_retrieve_popup_jquery', 7);
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Kloeke Georeference in Text'),   'kloeke_info_retrieve_popup_jquery', 7);

//                 add_filter(array('Display', 'Item', 'Dublin Core', 'Description'),                  'scroll_to_full_text');
                 add_filter(array('Display', 'Item', 'Dublin Core', 'Source'),                       'make_urls_clickable_in_text');
                 add_filter(array('Display', 'Item', 'Item Type Metadata', 'Literature'),            'make_urls_clickable_in_text');
                 add_filter(array('Display', 'Item', 'Dublin Core', 'Date'),                         'present_dates_as_language_admin', 20);
            }
         }
         if(isset($view->items) || isset($view->search)) {
             add_filter(array('Display', 'Item', 'Dublin Core', 'Date'),                         'present_dates_as_language', 20);
         }
     }

    /*  Super specific code for checking the "Privacy Required" value of a person 
    * without going through the official permission system.
    * A dirty dirty solution!
    *
    * Example: get_elements_private_status_by_value("Muiser, Iwe", [name field], [collection id]) (4=narrators)
    * @Returns boolean
    */
    private function get_elements_private_status_by_value($search_string, $element_name = "Title", $collection_id = 4){
        _log($search_string);
        $db = get_db();
    	$config = $db->getAdapter()->getConfig();
        $db_hack = new Zend_Db_Adapter_Pdo_Mysql(array( //call database for checking
        	'host'     => $config["host"],
        	'username' => $config["username"],
        	'password' => $config["password"],
        	'dbname'   => $config["dbname"]));
        if (!$db->getConnection()) {
            return false;
        }
        $sql = $this->illegal_sql_generator($search_string, false, $element_name, $collection_id);
        $stmt = $db_hack->prepare($sql);
		$stmt->execute();
		$itemId = $stmt->fetch();
    	if ($itemId){
    	    if (array_key_exists("id", $itemId)){
        	    $sql2 = $this->illegal_sql_generator(false, $itemId["id"], "Privacy Required", $collection_id);
                $stmt = $db_hack->prepare($sql2);
        		$stmt->execute();
        		$item = $stmt->fetch();
        		if ($item){
        	    	if (array_key_exists("text", $item)){
                        if ($item["text"] == "ja"){
                            return true;
                        }
                    }
                }
            }
    	}
    	return false;
    }

    /**
    *   This piece of code generates sql code to fetch items outside the safe environment of Omeka
    *
    *
    **/
    private function illegal_sql_generator($search_string, $item_id, $element_name, $collection_id){
        $db = get_db();
        $search_string = mb_convert_encoding($search_string, "CP1252", "UTF-8");
    	$sql = "
    	SELECT items.id, text
    	FROM {$db->Item} items 
    	JOIN {$db->ElementText} element_texts 
    	ON items.id = element_texts.record_id 
    	JOIN {$db->Element} elements 
    	ON element_texts.element_id = elements.id 
    	JOIN {$db->ElementSet} element_sets 
    	ON elements.element_set_id = element_sets.id 
    	AND elements.name = '" . $element_name . "'
        AND items.collection_id = '" . $collection_id . "'";
    	if ($search_string) {$sql .= "AND element_texts.text = '" . $search_string . "'"; }
    	if ($item_id) {$sql .= "AND items.id = '" . $item_id . "'"; }
    	return $sql;
    }


    public function filterPublicNavigationMain($args){
        #ONLY FOR NAVIGATION
    }
    
    public function filterPublicNavigationAdminBar($navLinks)
    {
        $view = get_view();
        if(isset($view->item)) {
            $record = $view->item;
            $aclRecord = $view->item;
        }

        if(isset($view->collection)) {
            $record = $view->collection;
            $aclRecord = $view->collection;
        }

        if(isset($view->simple_pages_page)) {
            $record = $view->simple_pages_page;
            $aclRecord = 'SimplePages_Page';
        }

        if(isset($view->exhibit_page)) {
            $record = $view->exhibit_page;
            $aclRecord = $view->exhibit;
        }                

        if(!isset($record)) {
            return $navLinks;
        }

        if(is_allowed($aclRecord, 'edit')) {
#            set_theme_base_url('admin');
            if(get_class($record) == 'ExhibitPage') {
                $url = admin_url('exhibits/edit-page-content/' . $record->id);
            } else {
                $url = url('admin/items/edit/' . $record->id);
            }
#            print "<pre>" . url('admin/items/edit/' . $record->id) . "</pre>";
            //want to place it first in the navigation, so do an array merge
            $editLinks['Edit Link'] = array(
                    'label'=>'Edit',
                    'uri'=> $url
                    );
            revert_theme_base_url();
            $navLinks = array_merge($editLinks, $navLinks);
        }
        return $navLinks;
    }
    
    
    /**
     * Appends some more stats to the dashboard
     * 
     * @return void
     **/
    function filterAdminDashboardStats($stats)
    {   
    	$vvcollection = get_record_by_id('Collection', 1);
        $stats[] = array(link_to_items_in_collection(metadata($vvcollection, 'total_items'), $props = array(), $action = 'browse', $collectionObj = $vvcollection), __('Folktales'));

    	$pcollection = get_record_by_id('Collection', 4);
        $stats[] = array(link_to_items_in_collection(metadata($pcollection, 'total_items'), $props = array(), $action = 'browse', $collectionObj = $vvcollection), __('Narrators'));

    	$ccollection = get_record_by_id('Collection', 9);
        $stats[] = array(link_to_items_in_collection(metadata($ccollection, 'total_items'), $props = array(), $action = 'browse', $collectionObj = $ccollection), __('Collectors'));

    	$tpcollection = get_record_by_id('Collection', 3);
        $stats[] = array(link_to_items_in_collection(metadata($tpcollection, 'total_items'), $props = array(), $action = 'browse', $collectionObj = $tpcollection), __('Folktale Types'));

    	$mcollection = get_record_by_id('Collection', 10);
        $stats[] = array(link_to_items_in_collection(metadata($mcollection, 'total_items'), $props = array(), $action = 'browse', $collectionObj = $mcollection), __('Motifs'));
        
        return $stats;
    }

    function print_pre($whatever){
    	print "<pre>";
    	print_r($whatever);
    	print "</pre>";
    }


    /**
     * Append search to dashboard
     * 
     * @return void
     **/
    function filterAdminDashboardPanels($panels){
        $panels2[] = $this->_addDashboardBrowseEtc($panels);
        $panels2[] = $this->_addDashboardSearchEtc($panels);
        $panels2[] = $this->_pimped_recent_items();
#        $panels2[] = $this->_active_users();
        return $panels2;
    }
    

    function _pimped_recent_items(){
        $recent_html = '<h2>' . __('Recent Items') . '</h2>';
        set_loop_records('items', get_recent_items(5));
            foreach (loop('items') as $item){
                $recent_html .= '<div class="recent-row">';
                $recent_html .= '<p class="recent">' . metadata($item, array('Dublin Core', 'Identifier')) . ' - '. link_to_item() . '</p>';
                $recent_html .= '<p class="recent">' . (metadata($item, 'item_type_name') ? metadata($item, 'item_type_name') : "NO ITEMTYPE!") . 
                                " in " . (metadata($item, 'collection_name') ? metadata($item, 'collection_name') : "NO COLLECTION !") . '</p>';
                if (is_allowed($item, 'edit')){
                    $recent_html .= '<p class="dash-edit">' . link_to_item(__('Edit'), array(), 'edit') . '</p>';
                }
                $recent_html .= '</div>';
            }
        return $recent_html;
    }
        
    function _addDashboardSearchEtc($panels){
#        $db = get_db();

        $zoeken_html = "<H1>Snelzoeken</H1><br>";

        $zoeken_html .= '<H2>Zoek in Volksverhalen</H2>';

        $zoeken_html .= '<form id="' . url(array('controller'=>'items', 'action'=>'browse')). '" action="/admin/items/browse" method="GET">';
        $zoeken_html .= '<label>Zoek in tekst</label><br>';
        $zoeken_html .= '<input type="hidden" name="keywordsearch[0][element_id]" id="keywordsearch[0][element_id]" value="1">';
        $zoeken_html .= '<input type="hidden" name="collection" id="collection" value="1" >';
        $zoeken_html .= '<input type="text" name="keywordsearch[0][terms]" id="keywordsearch[0][terms]" value="" size="30">';
        $zoeken_html .= '<input type="submit" class="submit small green button" name="submit_search" id="submit_search_advanced" value="';
        $zoeken_html .= __('search') . '">';
        $zoeken_html .= "</form>";

        $zoeken_html .= '<form id="' . url(array('controller'=>'items', 'action'=>'browse')). '" action="/admin/items/browse" method="GET">';
        $zoeken_html .= '<label>Zoek in tags</label><br>';
#        $zoeken_html .= '<input type="hidden" name="advanced[0][element_id]" id="advanced[0][element_id]" value="">';
        $zoeken_html .= '<input type="hidden" name="collection" id="collection" value="1" >';
        $zoeken_html .= '<input type="text" name="tags" id="tags" value="" size="30">';
        $zoeken_html .= '<input type="submit" class="submit small green button" name="submit_search" id="submit_search_advanced" value="';
        $zoeken_html .= __('search') . '">';
        $zoeken_html .= "</form>";

        /*zoeken in velden 63 65 66 (exclusive)*/
        $zoeken_html .= '<form id="' . url(array('controller'=>'items', 'action'=>'browse')). '" action="/admin/items/browse" method="GET">';
        $zoeken_html .= '<label>Zoek in named entities</label><br>';
        $zoeken_html .= '<select name="keywordsearch[0][element_id]" id="keywordsearch[0][element_id]" style="width: 140px">
                            <option value="63">Generiek (oud)</option>
                            <option value="66">Namen</option>
                            <option value="65">Plaatsen</option>
                        </select>';
#        $zoeken_html .= '<input type="hidden" name="advanced[0][element_id]" id="advanced[0][element_id]" value="63">';
#        $zoeken_html .= '<input type="hidden" name="advanced[0][type]" id="advanced[0][type]" value="contains">';
        $zoeken_html .= '<input type="text" name="keywordsearch[0][terms]" id="keywordsearch[0][terms]" value="" size="10">';
        $zoeken_html .= '<input type="submit" class="submit small green button" name="submit_search" id="submit_search_advanced" value="';
        $zoeken_html .= __('search') . '">';
        $zoeken_html .= "</form>";

        $zoeken_html .= '<H2>Zoek in Verhaaltypen</H2>';

        $zoeken_html .= '<form id="' . url(array('controller'=>'items', 'action'=>'browse')). '" action="/admin/items/browse" method="GET">';
        $zoeken_html .= '<label>Beschrijving</label><br>';
        $zoeken_html .= '<input type="hidden" name="keywordsearch[0][element_id]" id="keywordsearch[0][element_id]" value="41" >';
#        $zoeken_html .= '<input type="hidden" name="advanced[0][type]" id="advanced[0][type]" value="contains" >';
        $zoeken_html .= '<input type="hidden" name="collection" id="collection" value="3" >';
        $zoeken_html .= '<input type="text" name="keywordsearch[0][terms]" id="keywordsearch[0][terms]" value="" size="30">';
        $zoeken_html .= '<input type="submit" class="submit small green button" name="submit_search" id="submit_search_advanced" value="';
        $zoeken_html .= __('search') . '">';
        $zoeken_html .= "</form>";

        $zoeken_html .= '<form id="' . url(array('controller'=>'items', 'action'=>'browse')). '" action="/admin/items/browse" method="GET">';
        $zoeken_html .= '<label>Verhaaltypenummer (Aanduiding)</label>';
        $zoeken_html .= '<input type="hidden" name="keywordsearch[0][element_id]" id="keywordsearch[0][element_id]" value="43" >';
#        $zoeken_html .= '<input type="hidden" name="advanced[0][type]" id="advanced[0][type]" value="contains" >';
        $zoeken_html .= '<input type="hidden" name="collection" id="collection" value="3" >';
        $zoeken_html .= '<input type="text" name="keywordsearch[0][terms]" id="keywordsearch[0][terms]" value="" size="30">';
        $zoeken_html .= '<input type="submit" class="submit small green button" name="submit_search" id="submit_search_advanced" value="';
        $zoeken_html .= __('search') . '">';
        $zoeken_html .= "</form>";

        $zoeken_html .= '<H2>Zoek in Vertellers</H2>';

        $zoeken_html .= '<form id="' . url(array('controller'=>'items', 'action'=>'browse')). '" action="/admin/items/browse" method="GET">';
        $zoeken_html .= '<label>Op naam</label><br>';
        $zoeken_html .= '<input type="hidden" name="keywordsearch[0][element_id]" id="keywordsearch[0][element_id]" value="50" >';
#        $zoeken_html .= '<input type="hidden" name="advanced[0][type]" id="advanced[0][type]" value="contains" >';
        $zoeken_html .= '<input type="hidden" name="collection" id="collection" value="4" >';
        $zoeken_html .= '<input type="text" name="keywordsearch[0][terms]" id="keywordsearch[0][terms]" value="" size="30">';
        $zoeken_html .= '<input type="submit" class="submit small green button" name="submit_search" id="submit_search_advanced" value="';
        $zoeken_html .= __('search') . '">';
        $zoeken_html .= "</form>";

    	return $zoeken_html;
    }

    function _verhaaltype_lijst($maker){
        return url(array('module'=>'items','controller'=>'browse'), 
                                'default',
                                array("search" => "",
                                    "submit_search" => "Zoeken",
                                    "collection" => "3",
                                    "advanced[0][element_id]" => "39",
                                    "advanced[0][type]" => "is exactly",
                                    "advanced[0][terms]" => $maker,
                                    )
                                );
    }

    function _addDashboardBrowseEtc($panels){
        $all_tales_browse = url(array('module'=>'items','controller'=>'browse'), 
                                'default',
                                array("search" => "",
                                    "submit_search" => "Zoeken",
                                    "collection" => "1",
                                    )
                                );
        $private_tales_browse = url(array('module'=>'items','controller'=>'browse'), 
                                'default',
                                array("search" => "",
                                    "submit_search" => "Zoeken",
                                    "collection" => "1",
                                    "public" => "0",
                                    )
                                );
        $public_tales_browse = url(array('module'=>'items','controller'=>'browse'), 
                                'default',
                                array("search" => "",
                                    "submit_search" => "Zoeken",
                                    "collection" => "1",
                                    "public" => "1",
                                    )
                                );
        $own_tales_browse = url(array('module'=>'items','controller'=>'browse'), 
                                'default',
                                array("search" => "",
                                    "submit_search" => "Zoeken",
                                    "collection" => "1",
                                    "user" => current_user()->id
                                    )
                                );
        $item_toevoegen = 
        $folktale_html = "";

        $advanced_search = url(array('module'=>'solr-search','controller'=>'results', 'action' => 'search-form'), 
                                'default'
                                );
        $very_advanced_search = url(array('module'=>'items','controller'=>'search'), 
                                'default'
                                );

        $folktale_html .= "<H1>Volksverhalenbank functies</H1><br>";
        $folktale_html .= "<a class='small blue advanced-search-link button' href='$advanced_search'>Geavanceerd zoeken</a>";
        $folktale_html .= "<a class='small blue advanced-search-link button' href='$very_advanced_search'>ZEER Geavanceerd zoeken</a>";

        $verhalenkaart_url = url(array('module'=>'visuals','controller'=>'originalmap'), 
                                'default'
                                );
        $nodenetwork_url = url(array('module'=>'visuals','controller'=>'nodes'), 
                                'default'
                                );
                                
        $folktale_html .= "<H2>Geavanceerde Visualisaties</H2><br>";
        $folktale_html .= '<UL STYLE="list-style-type: disc;">';
        $folktale_html .= "<li><a target='originalmap' href='$verhalenkaart_url'>Orginele verhalenkaart visualisatie en zoekinterface</a><br>";
        $folktale_html .= "<li><a target='originalnetwork' href='$nodenetwork_url'>Originele verhalennetwerk visualisatie tool</a><br>";
        $folktale_html .= "</UL><br>";

        $folktale_html .= "<H2>Invoerhulp websites / lijsten</H2><br>";
        $folktale_html .= '<UL STYLE="list-style-type: disc;">';
        $folktale_html .= "<li><a target='manual' href='http://bookstore.ewi.utwente.nl/docs/Handleiding%20Nieuwe%20Volksverhalenbank%20Versie%202.pdf'><b>Handleiding</b> Invoer Nederlandse Volksverhalenbank 2014</a><br>";
        $folktale_html .= "<li><a target='manual' href='http://www.verhalenbank.nl/admin/items/show/125694'><b>Handleiding</b> Nederlandse Volksverhalenbank Handleiding 2016</a><br>";
        $folktale_html .= "<li><a target='motieven' href='http://www.dinor.demon.nl/Thompson/'>Browse/zoek <b>Thompson motieven boomstructuur</b> (website Dirk Kramer)</a><br>";
        $folktale_html .= "<li><a target='momfer' href='http://www.momfer.ml'>Browse/zoek <b>Thompson motieven thesaurus / lexical search</b> (Momfer van Folgert Karsdorp)</a><br>";
        $folktale_html .= "<li><a target='kloekenummers' href='http://www.meertens.knaw.nl/kloeke/'>Zoek <b>Kloeke nummers</b> (website Meertens)</a><br>";
        $folktale_html .= "</UL><br>";

        $folktale_html .= "<H2>Volksverhalen lijsten</H2><br>";
        $folktale_html .= '<UL STYLE="list-style-type: disc;">';
        $folktale_html .= "<li><a href = '$all_tales_browse'>Browse <b>alle</b> volksverhalen</a><br>";
        $folktale_html .= "<li><a href = '$private_tales_browse'>Browse <b>prive</b> volksverhalen</a><br>";
        $folktale_html .= "<li><a href = '$public_tales_browse'>Browse <b>publieke</b> volksverhalen</a><br>";
        $folktale_html .= "<li><a href = '$own_tales_browse'>Browse <b>zelf toegevoegde</b> volksverhalen</a><br>";
        $folktale_html .= "</ul><br>";

        $folktale_html .= "<H2>Volksverhaaltype lijsten</H2><br>";
        $folktale_html .= '<UL STYLE="list-style-type: disc;">';
        $folktale_html .= "<li><a href = '".$this->_verhaaltype_lijst("Theo Meder")."'>Browse <b>Theo Meder</b> Verhaaltypen</a><br>";
        $folktale_html .= "<li><a href = '".$this->_verhaaltype_lijst("ATU")."'>Browse <b>ATU</b> Verhaaltypen</a><br>";
        $folktale_html .= "<li><a href = '".$this->_verhaaltype_lijst("Aarne Thompson")."'>Browse <b>Aarne Thompson</b> Verhaaltypen</a><br>";
        $folktale_html .= "<li><a href = '".$this->_verhaaltype_lijst("Brunvand")."'>Browse <b>Brunvand</b> Verhaaltypen</a><br>";
        $folktale_html .= "<li><a href = '".$this->_verhaaltype_lijst("Sinninghe")."'>Browse <b>Sinninghe</b> Verhaaltypen</a><br>";
        $folktale_html .= "<li><a href = '".$this->_verhaaltype_lijst("Van der Kooi")."'>Browse <b>Van der Kooi</b> Verhaaltypen</a><br>";
        $folktale_html .= "</UL><br>";

        return $folktale_html;
    }

    function _count_items($collection = null)
    {
        if($collection) {
    		if(is_numeric($collection)) {
    		    $collectionId = $collection;
    		} else {
    		    $collectionId = $collection->id;
    		}
    		$count = get_db()->getTable('Item')->count(array('collection'=>$collectionId));
    	    } else {
    	        $count = get_db()->getTable('Item')->count();
        	}
        return $count;
    }
}

#add_filter(array('Display', 'Item', 'Item Type Metadata', 'Comments'), 'dummy_printer', 5);

function dummy_printer($args){
    return "DUMMY";
}

function make_urls_clickable_in_text($args){
    return preg_replace('#(\A|[^=\]\'"a-zA-Z0-9])(http[s]?://(.+?)/*[^()<>\s]+)#i', '\\1<a target="linked" href="\\2">\\2</a>', $args);
#	return url_to_link($args);
}

function my_kloeke_link_function($args){
    $kloeke_link = get_option('kloekelink') . $args;
    return "<a href='$kloeke_link'>$args</a>";
}

function my_motif_link_function($args){
    $motif_link = get_option('motiflink') . $args;
    return "<a href='$motif_link'>$args</a>";
}

/**
 * Return the site-wide search form.
 * 
 * @package Omeka\Function\Search
 * @param array $options Valid options are as follows:
 * - show_advanced (bool): whether to show the advanced search; default is false.
 * - submit_value (string): the value of the submit button; default "Submit".
 * - form_attributes (array): an array containing form tag attributes.
 * @return string The search form markup.
 */
function search_form_extended(array $options = array())
{
    return get_view()->searchForm($options);
}


?>