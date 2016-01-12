<?php
/**
 * Meta MetaData
 * 
 * @copyright Iwe Muiser for the Meertens Institute
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Filter forms and add metametadata input items
 * 
 * @package Omeka\Plugins\MetaMetaData
 */
class InputFieldFilter extends Zend_Controller_Plugin_Abstract
{
    /**
     * All routes that render an item element form, including those requested 
     * via AJAX.
     * 
     * @var array
     */
    protected $_defaultRoutes = array(
        array('module' => 'default', 'controller' => 'items', 
              'actions' => array('annotate', 'add', 'edit', 'change-type')), 
        array('module' => 'default', 'controller' => 'elements', 
              'actions' => array('element-form')), 
    );
    
    /**
     * Set the filters pre-dispatch only on configured routes.
     * @param Zend_Controller_Request_Abstract
     */
    public function preDispatch($request)
    {
        $db = get_db();
        
        // Some routes don't have a default module, which resolves to NULL.
        $currentModule = is_null($request->getModuleName()) ? 'default' : $request->getModuleName();
        $currentController = $request->getControllerName();
        $currentAction = $request->getActionName();
        
        // Allow plugins to register routes that contain form inputs rendered by 
        // Omeka_View_Helper_ElementForm::_displayFormInput().
        $routes = apply_filters('folktale_annotator_routes', $this->_defaultRoutes);
        
        // Apply filters to defined routes.
        foreach ($routes as $route) {
            
            // Check registered routed against the current route.
            if ($route['module'] != $currentModule 
             || $route['controller'] != $currentController 
             || !in_array($currentAction, $route['actions'])){
                continue;
            }
            //For folktale item types
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Text"), array($this, 'filterElementInputLargeTextareaRequired'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Collector"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Motif"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Named Entity"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Named Entity Actor"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Named Entity Location"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Kloeke Georeference"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Kloeke Georeference in Text"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Corpus"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Entry Date"), array($this, 'filterElementInputOneLine'), 10);
            //For person item types
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Birthplace"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Birth Date"), array($this, 'filterElementInputDate'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Death Date"), array($this, 'filterElementInputDate'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Place of Residence"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Place of Residence Since Date"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Previous Place of Residence"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Occupation"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Religion"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Date Visited"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Name Mother"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Birthplace Mother"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Occupation Mother"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Name Father"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Birthplace Father"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Birthdate Father"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Occupation Father"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Family Relations"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Married to"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Name partner"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Birthplace partner"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Birthdate partner"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Occupation partner"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Bibliography"), array($this, 'filterElementInputOneLine'), 10);
            //Folktale type item types
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Original Tale Type"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Category"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Subcategory"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Literature"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Item Type Metadata", "Combinations"), array($this, 'filterElementInputOneLine'), 10);
            //Dublin core item types
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Title"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Identifier"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Description"), array($this, 'filterElementInputLargeTextarea'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Subject"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Creator"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Contributor"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Date"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Relation"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Publisher"), array($this, 'filterElementInputOneLine'), 10);
            add_filter(array('ElementInput', 'Item', "Dublin Core", "Format"), array($this, 'filterElementInputOneLine'), 10);
        }
    }

    
    public function filterElementInputLargeTextareaRequired($components, $args){
        $components['input'] = get_view()->formTextarea($args['input_name_stem'] . '[text]', $args['value'], array('rows' => '20', 'required' => ''));
        return $components;
    }

    public function filterElementInputDate($components, $args){
        $components['input'] = get_view()->formTextarea($args['input_name_stem'] . '[text]', $args['value'], array('rows' => '1'));
        return $components;
    }

    public function filterElementInputOneLine($components, $args){
        $components['input'] = get_view()->formTextarea($args['input_name_stem'] . '[text]', $args['value'], array('rows' => '1'));
        return $components;
    }
        
    public function filterElementInputLargeTextarea($components, $args){
        $components['input'] = get_view()->formTextarea($args['input_name_stem'] . '[text]', $args['value'], array('rows' => '20'));
        return $components;
    }
    
    
    public function print_enter($doit){
        print "\n--";
        print_r($doit);
        print "--\n";
    }

    public function print_txt($args){ 
        print "<textarea rows = 6>";
        print_r($args);
        print "</textarea>";
    }

}
