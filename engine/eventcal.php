<?php

define('EVENTTAB', 'events'  );
define('HOLITAB' , 'holidays');

/**
    Event table:
    ============
    id:     identifier auto_increment created automatically
    date:   date string in form Y-m-d (e.g. 2014-11-11)
    time:   time string in form H:i (e.g. 14:44)
    name:   event name string
    descr:  event description
    where:  place
  
    create code:
    Table::create(EVENTTAB, array('date', 'time', 'name', 'descr', 'where'));

    Holiday table:
    ==============
    id:     identifier auto_increment created automatically
    day:    integer used as day (fix == no), or offset (fix == yes)
    month:  integer used as month (fix == no), or unused (fix == yes)
    name:   string for holiday name
    fix:    string "yes" -> fix date, "no" -> variable date

    create code:
    Table::create(HOLITAB, array('day', 'month', 'name', 'fix'));
*/

/**
 * @class      EventCal
 * @copyright  (c) 2014, Jo2003
 * @date       21.11.2014
 * @brief      implemenatation of an event calendar for Monstra CMS
 */
class EventCal 
{
    /**
     *
     * @var int
     */
    private $year;

    /**
     *
     * @var DateTime easter day 
     */
    private $easter;

    /**
     *
     * @var Table holiday table
     */
    private $holTab;

    /**
     *
     * @var Table event table
     */
    private $evtTab;

    /**
     * array with all holidays of this year in form:
     * <code>
     *        array('date'  => '2014-11-11', 
     *              'wday'  => 1 ... 7,
     *              'mday'  => 1 ... 31,
     *              'month' => 1 ... 12,
     *              'name'  => 'Holiday name')
     * </code>
     * @var array
     */
    private $holiArray;
    
    /**
     * array with all events of this year (for caching)
     * @var array
     */
    private $evtArray;

    /**
     * constructor
     * @author  Jo2003
     * @date    21.11.2014
     * @param   int $year [in] (int) optional year
     */
    function __construct($year = "now")
    {
        // set year ...
        if ($year === "now")
        {
            $this->year = (int)date("Y");
        }
        else
        {
            $this->year = (int)$year;   
        }

        // get easter date ...
        $orthodox     = (Option::get('churchcalendar') === 'orthodox') ? TRUE : FALSE;
        $this->easter = self::easterDay($this->year, $orthodox);
        $this->holTab = new Table(HOLITAB);
        $this->evtTab = new Table(EVENTTAB);

        $this->fillholiArray();
        $this->fillEvtArray();
    }
    
    /**
     * get easter date
     * @param int $year
     * @param boolean $orthodox
     * @return \DateTime
     */
    public static function easterDay($year, $orthodox = FALSE)
    {
        $easter = null;
        $doffs  = 0;
        
        if ($orthodox)
        {
            // method of GAUSS
            $easter = new DateTime($year."-04-03");
            $r1     = $year % 19; 
            $r2     = $year % 4; 
            $r3     = $year % 7; 
            $ra     = 19 * $r1 + 16; 
            $r4     = $ra % 30; 
            $rb     = 2 * $r2 + 4 * $r3 + 6 * $r4; 
            $r5     = $rb % 7; 
            $doffs  = $r4 + $r5; 
        }
        else
        {
            $easter = new DateTime($year."-03-21");
            $doffs  = easter_days($year);
        }
        
        $intv = new DateInterval("P".$doffs."D");
        
        if ($doffs > 0)
        {
            $easter->add($intv);
        }
        else 
        {
            $easter->sub($intv);
        }
        
        return $easter;
    }

    /**
     * destructor / cleanup
     * @author  Jo2003
     * @date    21.11.2014
     */
    function __destruct()
    {
        $this->year      = 0;
        $this->easter    = null;
        $this->holTab    = null;
        $this->evtTab    = null;
        $this->holiArray = array();
    }

    /**
     * create needed tables [static]
     * @author  Jo2003
     * @date    21.11.2014
     */
    public static function createTables()
    {
        if (!Table::get(EVENTTAB))
        {
            Table::create(EVENTTAB, array('date', 'time', 'name', 'descr', 'where'));
        }

        if (!Table::get(HOLITAB))
        {
            Table::create(HOLITAB, array('day', 'month', 'name', 'fix'));
        }
    }
    
    /**
     * drop needed tables [static]
     * @author  Jo2003
     * @date    21.11.2014
     */
    public static function dropTables()
    {
        if (Table::get(EVENTTAB))
        {
            Table::drop(EVENTTAB);
        }

        if (Table::get(HOLITAB))
        {
            Table::drop(HOLITAB);
        }
    }

    /**
     * get curently used year
     * @author  Jo2003
     * @date    21.11.2014
     * @return  int year
     */
    public function year()
    {
        return $this->year;
    }

    /**
     * check if given date is a holiday
     * @author  Jo2003
     * @date    21.11.2014
     * @param   string $datestring date string in form "Y-m-d"
     * @return  string holiday name; 
     * @return  bool FALSE if not a holiday
     */
    public function isHoliday($datestring)
    {
        $ret = FALSE;
        
        foreach ($this->holiArray as $hd) 
        {
            if ($hd['date'] === $datestring)
            {
                $ret = $hd['name'];
                break;
            }
        }
        
        return $ret;
    }

    /**
     * create month array
     * @param int $month month number 1 ... 12
     * @return array month array
     */
    public function monthArray($month)
    {
        $monthArray = array();
        $dayArray   = array();
        $dstring    = sprintf("%d-%.02d-01", $this->year, (int)$month);
        
        if (($day = new DateTime($dstring)) !== FALSE)
        {
            $iv      = new DateInterval("P1D");

            while ((int)$month === (int)$day->format("m"))
            {
                $dayArray = array(
                    'date'    => $day->format("Y-m-d"),
                    'mday'    => (int)$day->format("d"),
                    'wday'    => (int)$day->format("N"),
                    'month'   => (int)$day->format("m"),
                    'holiday' => $this->isHoliday($day->format("Y-m-d")),
                    'events'  => $this->dayEvents($day->format("Y-m-d"))
                );
                
                $monthArray[] = $dayArray;

                $day->add($iv);

            }
        }

        return $monthArray;
    }

    /**
     * create month arrays for this year
     * @return array array of month arrays
     */
    public function yearArray()
    {
        $yearArray = array();

        for ($i = 1; $i <= 12; $i++)
        {
            $yearArray[] = $this->monthArray($i);
        }
        
        return $yearArray;
    }

    /**
     * creates a new instance of EventCal
     * @param type $year
     * @return EventCal new instance
     */
    public static function getCalendar($year = "now")
    {
        $ec = new EventCal($year);
        return $ec;
    }

    /**
     * add a variable (easter based) holiday
     * @param int $offset offset counting from easter sunday
     * @param string $name holidays name
     * @param int $id for update
     * @return bool FALSE on error; else success
     */
    public static function addVarHoliday ($offset, $name, $id = -1)
    {
        $ret = FALSE;
        $ht  = new Table(HOLITAB);

        $data = array(
            'day'   => $offset,
            'month' => -1,
            'name'  => XML::safe($name),
            'fix'   => 'no'
        );
        
        if ($id == -1)
        {
            $ret = $ht->insert($data);
        }
        else 
        {
            $ret = $ht->update($id, $data);
        }

        return $ret;
    }

    /**
     * add a fix date based holiday
     * @param int $day 1 ... 31
     * @param int $month 1 ... 12
     * @param string $name name of holiday
     * @param int $id for update
     * @return bool FALSE on error; else success
     */
    public static function addFixHoliday ($day, $month, $name, $id = -1)
    {
        $ret = FALSE;
        $ht  = new Table(HOLITAB);

        $data = array(
            'day'   => $day,
            'month' => $month,
            'name'  => XML::safe($name),
            'fix'   => 'yes'
        );
        
        if ($id == -1)
        {
            $ret = $ht->insert($data);
        }
        else 
        {
            $ret = $ht->update($id, $data);
        }

        return $ret;
    }

    /**
     * create new event entry
     * @param string $datetime date string in form "Y-m-d H:i"
     * @param string $name name of event
     * @param string $where place
     * @param string $descr event description
     * @param int $id optional entry id; used on update
     * @return bool FALSE on error; else success
     */
    public static function addEvent ($datetime, $name, $where, $descr = "", $id = -1)
    {
        $ret = FALSE;
        $et  = new Table(EVENTTAB);

        if (($dt = new DateTime($datetime)) !== FALSE)
        {
            $data = array(
                'date'  => $dt->format("Y-m-d"),
                'time'  => $dt->format("H:i"),
                'name'  => XML::safe($name),
                'descr' => XML::safe($descr),
                'where' => XML::safe($where)
            );
            
            if ($id != -1)
            {
                // update entry ...
                if ($et->update($id, $data))
                {
                    $ret = $id;
                }
            }
            else
            {
                // insert new entry ...
                if ($et->insert($data))
                {
                    $ret = $et->lastId();
                }
            }
        }

        return $ret;
    }

    /**
     * delete event based in entry id
     * @param int $id entry id
     * @return bool FALSE on error; else success
     */
    public static function delEvent ($id)
    {
        $et = new Table(EVENTTAB);

        // only delete ...
        return $et->delete($id);
    }

    /**
     * delete holiday based on entry id
     * @param int $id entry id
     * @return bool FALSE on error; else success
     */
    public static function delHoliday ($id)
    {
        $ht = new Table(HOLITAB);

        // only delete ...
        return $ht->delete($id);
    }

    /**
     * 
     * @param int $id entry id
     * @return array
     */
    public static function holiday($id)
    {
        $ht = new Table(HOLITAB);
        return $ht->select("[id=".$id."]");
    }
    
    /**
     * get holidays as stored in database
     * @return array
     */
    public static function holidays()
    {
        $ht = new Table(HOLITAB);
        return $ht->select();
    }

    /**
     * get events for one day
     * @param string $datestring date as string in form "Y-m-d"
     * @return array
     */
    public function dayEvents($datestring)
    {
        $devents = array();
        
        if (array_key_exists($datestring, $this->evtArray))
        {
            $devents = $this->evtArray[$datestring];
        }
        
        return $devents;
    }
    
    /**
     * 
     * @param int $id entry id
     * @return array
     */
    public static function event($id)
    {
        $et = new Table(EVENTTAB);
        return $et->select("[id=".$id."]");
    }

    /**
     * get events for one / all months in this year
     * @param int $month optional month number 1 ... 12
     * @return array
     */
    public function events($month = "all")
    {
        $evts = array();
        
        foreach($this->evtArray as $events) 
        {
            $dt = new DateTime($events['date']);
            
            if (($month === 'all') || ((int)$month === (int)$dt->format("m")))
            {
                foreach ($events as $event)
                {
                    $evts[] = $event;
                }
            }
        }
        
        return $evts;
    }
    
    /**
     * fill an array with all year events (for caching reason)
     */
    public function fillEvtArray()
    {
        $this->evtArray = array();

        $now    = new DateTime($this->year."-02-02");
        $events = $this->evtTab->select(null, 'all', null, null, 'time');
        
        foreach ($events as $event)
        {
            $date = new DateTime($event['date']);
            
            if ((int)$date->format("Y") === (int)$now->format("Y"))
            {
                $this->evtArray[$event['date']][] = $event;
            }
        }
        
        ksort($this->evtArray);
    }

    /**
     * create holiday array for this year
     * @return bool FALSE on error
     * @return array holiday array
     */
    public function fillholiArray()
    {
        $this->holiArray = array();
        $data            = array();
        if (($hds = EventCal::holidays()) !== FALSE)
        {
            foreach ($hds as $hd) 
            {
                if ($hd['fix'] == 'no')
                {
                    // clone easter day ...
                    $eastmp = clone($this->easter);
                    
                    // get offset as interval
                    $iv     = new DateInterval("P".abs($hd['day'])."D");

                    if ($hd['day'] < 0)
                    {
                        $eastmp->sub($iv);
                    }
                    else if ($hd['day'] > 0)
                    {
                        $eastmp->add($iv);
                    }

                    $data = array(
                        'date'  => $eastmp->format("Y-m-d"),
                        'wday'  => (int)$eastmp->format("N"),
                        'mday'  => (int)$eastmp->format("d"),
                        'month' => (int)$eastmp->format("m"),
                        'name'  => $hd['name']
                    );
                }
                else
                {
                    $dstring = sprintf("%d-%.02d-%.02d", 
                                       (int)$this->year,
                                       (int)$hd['month'],
                                       (int)$hd['day']);
                    
                    if (($dt = new DateTime($dstring)) !== FALSE)
                    {
                        $data = array(
                            'date'  => $dt->format("Y-m-d"),
                            'wday'  => (int)$dt->format("N"),
                            'mday'  => (int)$dt->format("d"),
                            'month' => (int)$dt->format("m"),
                            'name'  => $hd['name']
                        );
                    }
                }

                $this->holiArray[] = $data;
            }
        }

        return (count($this->holiArray) > 0) ? $this->holiArray : FALSE;
    }
    
    /**
     * create an array of all years with events
     * @return array filled with year numbers
     */
    public static function allYears()
    {
        $years = array(
                (date("Y") - 1) => 1, 
                 date("Y")      => 1, 
                (date("Y") + 1) => 1);

        if (Table::get(EVENTTAB))
        {
            $et    = new Table(EVENTTAB);
            $allev = $et->select(null, 'all', null, array('date'), 'date');
            
            foreach ($allev as $ev) 
            {
                $y = new DateTime($ev['date']);
                $years[$y->format("Y")] = 1;
            }
        }
        
                
        ksort($years);
        
        return $years;
    }
}
