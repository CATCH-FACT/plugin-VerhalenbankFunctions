<?php

/**
 * Does some magic with dates and date ranges
 * 
 * @package Omeka\View\Helper
 */
class DateFormatRange{
    
    public $free_text_date;
    public $date_span;
    public $date_start;
    public $date_end;
    public $valid = true;
    public $fixed = false;
    
    function __construct($free_text_date){
        $this->free_text_date = $free_text_date;
        $this->validate();
    }
    
    function validate(){
        $this->valid = true;
    }
}

/**
 * Does some magic with dates and date ranges
 * 
 * @package Omeka\View\Helper
 */
class DateFormatHuman{
    
    public $date_span;
    public $date_start;
    public $date_end;
    public $valid = true;
    public $fixed = false;
    
    //EEUW: eindwaarden
    public $century_quarters = array(
        25 =>   "Eerste kwart",             //25 jaar
        50 =>   "Tweede kwart",
        75 =>   "Derde kwart",
        0 =>   "Vierde kwart");

    //EEUW: eindwaarden
    public $century_positions = array(
        20 =>   "Begin",                    //20 jaar
        60 =>   "Midden",
        0 =>   "Eind");
        
    public $century_approx   =  50;         //50 jaar
    
    //JAAR: perioden in een jaar (op bassis van maannummers)
    public $year_quarters = array(
        "1 3" =>    "Eerste kwartaal",
        "4 6" =>    "Tweede kwartaal",
        "8 9" =>    "Derde kwartaal",
        "10 12" =>  "Vierde kwartaal",
        "1 2" =>    "Begin",
        "5 6" =>    "Midden",
        "11 12" =>  "Eind"
        );
    
    public $seasons = array();

    /**
     * constructs the class
     */ 
    function __construct($date_span){
        try {
            $this->seasons[date("m-d", mktime(0, 0, 0, 6, 21, 0)) . " " . date("m-d", mktime(0, 0, 0, 9, 22, 0))] = "Zomer";
            $this->seasons[date("m-d", mktime(0, 0, 0, 9, 23, 0)) . " " . date("m-d", mktime(0, 0, 0, 12, 21, 0))] = "Herfst";
            $this->seasons[date("m-d", mktime(0, 0, 0, 12, 22, 0)) . " " . date("m-d", mktime(0, 0, 0, 3, 20, 0))] = "Winter";
            $this->seasons[date("m-d", mktime(0, 0, 0, 3, 21, 0)) . " " . date("m-d", mktime(0, 0, 0, 6, 20, 0))] = "Lente";
            $this->date_span = $date_span;
            $this->validate();
            if ($this->valid){
                $date_span = explode(' ', $this->date_span, 2);
                $this->date_start = new DateTime($date_span[0]);
                $this->date_end = new DateTime($date_span[1]);
            }
            else{
                $this->date_start = $this->recoverDate($date_span) ? new DateTime($this->recoverDate($date_span)) : "Invalid date(range)";
            }
        } catch (Exception $e) {
            $this->date_start = $date_span;
            $this->fixed = False;
            echo 'incorrect date: ',  $e->getMessage();
        }
    }
    
    function recoverDate($date_span){
        if (preg_match('/^\d{4}.\\d{2}.\\d{2}/', $date_span, $matches)){
            $this->fixed = true;
            return $matches[0];
        }
        else{
            $this->fixed = false;
            return false;
        }
    }
    
    
    /**
     * returns a human readable formatted date (range)
     * @return string
     */
    function formatHuman(){
        if ($this->fixed) return $this->nlDate(strftime("%A %d %B %Y", strtotime($this->date_start->format('Ymd')))) . "";
        if (!isset($this->date_span)) {return "";}
        if (!$this->valid) return $this->date_span . " (foutieve datum)";
        else if ($this->is_identical($this->date_start, $this->date_end)){ //only one date
            return $this->nlDate(strftime("%A %d %B %Y", strtotime($this->date_start->format('Ymd'))));
        }
        else if ($this->is_century($this->date_start, $this->date_end)){ //full century
            return ($this->date_end->format('Y')/100) . "e eeuw";
        }
        else if ($this->is_quarterof_century($this->date_start, $this->date_end)){ //quarter century
            $century = floor($this->date_start->format('Y')/100);
            return $this->century_quarters[$this->date_end->format('Y') - $century * 100] . " " . ($century+1) . "e eeuw";
        }
        else if ($this->is_positionin_century($this->date_start, $this->date_end)){ //part of century
            $century = floor($this->date_start->format('Y')/100);
            return $this->century_positions[$this->date_end->format('y')] . " " . ($century+1) . "e eeuw";
        }
        else if ($this->is_year($this->date_start, $this->date_end)){ //a year
            return $this->date_end->format('Y');
        }
        else if ($this->is_partof_year_dyn($this->date_start, $this->date_end)){ //part of a year
            return $this->year_quarters[$this->date_start->format('n') . " " . $this->date_end->format('n')] . " " . $this->date_start->format('Y');
        }
        else if ($this->is_year_to_year($this->date_start, $this->date_end)){ //any other year to year with start = 1 jan and end = 31 dec
            return $this->date_start->format('Y') . " - " . $this->date_end->format('Y');
        }
        else if ($this->is_month_in_year($this->date_start, $this->date_end)){ //any month in a year with start = 1 m and end = n m
            return $this->nlDate(strftime("%B %Y", strtotime($this->date_start->format('Ymd'))));
        }
        else if ($this->is_season_in_year($this->date_start, $this->date_end)){ //a season in a year
            return $this->seasons[$this->date_start->format('m-d') . " " . $this->date_end->format('m-d')] . " " . $this->date_end->format('Y');
        }
        else{
            return "Van " . $this->nlDate(strftime("%A %d %B %Y", strtotime($this->date_start->format('Ymd')))) . " t/m " . $this->nlDate(strftime("%A %d %B %Y", strtotime($this->date_end->format('Ymd'))));
        }
    }
    
    
    /**
     * checks if date_start : YYYY-MM-01 AND date_end : YYYY-(MM+1)-31 
     * @return string
     */
    function is_season_in_year($date_start, $date_end){
        if (array_key_exists($date_start->format('m-d') . " " . $date_end->format('m-d'), $this->seasons)){
            return true;
        }
        else return false;
    }
    
    /**
     * checks if date_start : YYYY-MM-01 AND date_end : YYYY-(MM+1)-31 
     * @return string
     */
    function is_month_in_year($date_start, $date_end){
        if ((date("Y-m-d", mktime(0, 0, 0, $date_start->format('m')+1, 0, $date_start->format('Y'))) == $date_end->format('Y-m-d')) AND
            (date("Y-m-d", mktime(0, 0, 0, $date_start->format('m'), 1, $date_start->format('Y'))) == $date_start->format('Y-m-d'))){
            return true;
        }
        else return false;
    }
        
    /**
     * checks if date_start : YYYY-01-01 AND date_end : XXXX-12-31 
     * @return string
     */
    function is_year_to_year($date_start, $date_end){
        if ((date("Y-m-d", mktime(0, 0, 0, 13, 0, $date_end->format('Y'))) == $date_end->format('Y-m-d')) AND
            (date("Y-m-d", mktime(0, 0, 0, 1, 1, $date_start->format('Y'))) == $date_start->format('Y-m-d'))){
            return true;
        }
        else return false;
    }

    /**
     * checks if date_start : [(100(C-1))+1]-01-01 AND date_start : [100C]-12-DD 
     * @return string
     */
    function is_partof_year($date_start, $date_end){
        if (array_key_exists($date_start->format('m-d') . " " . $date_end->format('m-d'), $this->year_quarters) AND
                ($date_end->format('Y') - $date_start->format('Y') == 24)){
            return true;
        }
        else return false;
    }

    /**
     * checks if date_start : [(100(C-1))+1]-01-01 AND date_start : [100C]-12-DD 
     * @return string
     */
    function is_partof_year_dyn($date_start, $date_end){
        foreach($this->year_quarters as $months => $value){
            $values = explode(" ", $months);
            if ((date("Y-m-d", mktime(0, 0, 0, $values[0], 1, $date_start->format('Y'))) == $date_start->format('Y-m-d')) AND
                (date("Y-m-d", mktime(0, 0, 0, $values[1]+1, 0, $date_end->format('Y'))) == $date_end->format('Y-m-d')) AND
                ($date_end->format('Y') == $date_start->format('Y'))){
                return true;
            }
        }
        return false;
    }

    /**
     * checks if date_start : YYYY-01-01 AND date_start : YYYY-12-31 
     * @return string
     */
    function is_year($date_start, $date_end){
        if ((date("Y-m-d", mktime(0, 0, 0, 13, 0, $date_end->format('Y'))) == $date_end->format('Y-m-d')) AND
            (date("Y-m-d", mktime(0, 0, 0, 1, 1, $date_end->format('Y'))) == $date_start->format('Y-m-d'))){
            return true;
        }
        else return false;
    }

    /**
     * checks if date_start : [(100(C-1))+1]-01-01 AND date_start : [100C]-12-DD 
     * @return string
     */
    function is_positionin_century($date_start, $date_end){
        if (array_key_exists($date_end->format('y'), $this->century_positions) AND ($date_end->format('Y') - $date_start->format('Y') == 19)){
            return true;
        }
        else return false;
    }

    /**
     * checks if date_start : [(100(C-1))+1]-01-01 AND date_start : [100C]-12-DD 
     * @return string
     */
    function is_quarterof_century($date_start, $date_end){
        if ((date("m-d", mktime(0, 0, 0, 13, 0, $date_end->format('Y'))) == $date_end->format('m-d')) AND
            (date("m-d", mktime(0, 0, 0, 1, 1, $date_start->format('Y'))) == $date_start->format('m-d')) AND
            $date_end->format('Y') - $date_start->format('Y') == 24){
            return true;
        }
        else return false;
    }
    
    /**
     * checks if date_start : [(100(C-1))+1]-01-01 AND date_start : [100C]-12-DD 
     * @return string
     */
    function is_century($date_start, $date_end){
        if ($date_end->format('Y') - $date_start->format('Y') == 99 && $date_start->format('y') == 1){
            return true;
        }
        else return false;
    }
    
    /**
     * compares the years and returns a verdict
     * @return string
     */
    function is_identical($date_start, $date_end){
        $interval = $date_start->diff($date_end);
        return $interval->format("%a") == 0 ? true : false;
    }
    
    function checkData($mydate) { 

        list($yy,$mm,$dd) = explode("-",$mydate); 
        if (is_numeric($yy) && is_numeric($mm) && is_numeric($dd)) 
        { 
            return checkdate($mm,$dd,$yy); 
        } 
        return false;            
    }
    
    function validate(){
        if (preg_match('/^\d{4}.\\d{2}.\\d{2}\s\d{4}.\\d{2}.\\d{2}$/', $this->date_span)){
            $date_span = explode(' ', $this->date_span, 2);
            list($yy,$mm,$dd) = explode("-",$date_span[0]);
            list($yy2,$mm2,$dd2) = explode("-",$date_span[1]);
            if(checkdate($mm,$dd,$yy) AND checkdate($mm2,$dd2,$yy2)){
                $this->valid = true;
            }
            else{
                $this->valid = false;
            }
        }
        else{
            $this->valid = false;
        }
    }
    
    function nlDate($parameters){

        $datum = $parameters;
        
        // Vervang de maand, klein
        $datum = str_replace("january", "januari", $datum);
        $datum = str_replace("february", "februari", $datum);
        $datum = str_replace("march", "maart", $datum);
        $datum = str_replace("april", "april", $datum);
        $datum = str_replace("may", "mei", $datum);
        $datum = str_replace("june", "juni", $datum);
        $datum = str_replace("july", "juli", $datum);
        $datum = str_replace("august", "augustus", $datum);
        $datum = str_replace("september", "september", $datum);
        $datum = str_replace("october", "oktober", $datum);
        $datum = str_replace("november", "november", $datum);
        $datum = str_replace("december", "december", $datum);

        // Vervang de maand, hoofdletters
        $datum = str_replace("January", "Januari", $datum);
        $datum = str_replace("February", "Februari", $datum);
        $datum = str_replace("March", "Maart", $datum);
        $datum = str_replace("April", "April", $datum);
        $datum = str_replace("May", "Mei", $datum);
        $datum = str_replace("June", "Juni", $datum);
        $datum = str_replace("July", "Juli", $datum);
        $datum = str_replace("August", "Augustus", $datum);
        $datum = str_replace("September", "September", $datum);
        $datum = str_replace("October", "Oktober", $datum);
        $datum = str_replace("November", "November", $datum);
        $datum = str_replace("December", "December", $datum);

        // Vervang de maand, kort
        $datum = str_replace("Jan", "Jan", $datum);
        $datum = str_replace("Feb", "Feb", $datum);
        $datum = str_replace("Mar", "Maa", $datum);
        $datum = str_replace("Apr", "Apr", $datum);
        $datum = str_replace("May", "Mei", $datum);
        $datum = str_replace("Jun", "Jun", $datum);
        $datum = str_replace("Jul", "Jul", $datum);
        $datum = str_replace("Aug", "Aug", $datum);
        $datum = str_replace("Sep", "Sep", $datum);
        $datum = str_replace("Oct", "Ok", $datum);
        $datum = str_replace("Nov", "Nov", $datum);
        $datum = str_replace("Dec", "Dec", $datum);

        // Vervang de dag, klein
        $datum = str_replace("monday", "maandag", $datum);
        $datum = str_replace("tuesday", "dinsdag", $datum);
        $datum = str_replace("wednesday", "woensdag", $datum);
        $datum = str_replace("thursday", "donderdag", $datum);
        $datum = str_replace("friday", "vrijdag", $datum);
        $datum = str_replace("saturday", "zaterdag", $datum);
        $datum = str_replace("sunday", "zondag", $datum);

        // Vervang de dag, hoofdletters
        $datum = str_replace("Monday", "Maandag", $datum);
        $datum = str_replace("Tuesday", "Dinsdag", $datum);
        $datum = str_replace("Wednesday", "Woensdag", $datum);
        $datum = str_replace("Thursday", "Donderdag", $datum);
        $datum = str_replace("Friday", "Vrijdag", $datum);
        $datum = str_replace("Saturday", "Zaterdag", $datum);
        $datum = str_replace("Sunday", "Zondag", $datum);

        // Vervang de verkorting van de dag, hoofdletters
        $datum = str_replace("Mon",	 "Maa", $datum);
        $datum = str_replace("Tue", "Din", $datum);
        $datum = str_replace("Wed", "Woe", $datum);
        $datum = str_replace("Thu", "Don", $datum);
        $datum = str_replace("Fri", "Vri", $datum);
        $datum = str_replace("Sat", "Zat", $datum);
        $datum = str_replace("Sun", "Zon", $datum);

        return $datum;
    }
    
}

function subtest($date_span){
    $printable = new DateFormatHuman($date_span);
    print $date_span;
    print "<br>_______________________";
    print $printable->formatHuman();
    print "<br>";
}

function test(){    

    echo "CORRECTE DATUM RANGES DIE TOT VERTALING LEIDEN:<br>";
    subtest("1982-05-11 1982-05-11");
    subtest("1901-01-01 2000-12-31");
    subtest("1901-01-01 1925-12-31");
    subtest("1251-01-01 1275-12-31");
    subtest("1901-01-01 1901-12-31");
    subtest("1901-01-01 1901-03-31");
    subtest("1901-01-01 1920-03-31");
    subtest("1941-01-01 1960-03-31");
    subtest("1901-01-01 1901-02-28");
    subtest("1901-01-01 1901-02-31");
  
    subtest("1401-01-01 1450-12-31");
    subtest("1426-01-01 1450-12-31");    
    
    subtest("1401-01-01 1401-01-31");
    subtest("2001-08-01 2001-08-31");
    
    echo "WILLEKEURIGE DATUM RANGES:<br>";
    subtest("1951-01-01 1970-03-31");
    subtest("1982-05-11 1982-05-13");
    subtest("1426-01-01 1450-03-31");
    subtest("1401-01-01 1450-03-31");
    subtest("1401-01-01 1425-01-31");
                
    echo "FOUTIEVE DATUM RANGES:<br>";
    subtest("blablabla");
    subtest("1982-05-11 1982-0");
    subtest("1982-05-");
    subtest("2001-01-01 20  01-01-01");
    subtest("1941-01-01 1960-03-34");
    subtest("1978-04-14, 1978-04-21 1978-04-21");
    subtest("2011-31-10");
}

//test();

?>