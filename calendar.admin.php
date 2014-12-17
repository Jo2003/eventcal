<?php require_once PLUGINS.DS.'calendar'.DS.'engine'.DS.'eventcal.php';

// Admin Navigation: add new item
Navigation::add(__('Calendar', 'eventcal'), 'content', 'calendar', 4);

/**
 * Backend derived class for calendar administration
 */
class CalendarAdmin extends Backend 
{
    /**
     * main calendar
     * @var EventCal
     */
    private static $maincal  = null;
    
    /**
     * check main calendar class
     * @param int $year
     */
    private static function checkEventCal($year)
    {
        if (self::$maincal === null)
        {
            self::$maincal = new EventCal($year);
        }
        else 
        {
            if (self::$maincal->year() != $year)
            {
                self::$maincal = null;
                self::$maincal = new EventCal($year);
            }
        }
    }

    /**
     * Main Calendar admin function
     */
    public static function main() 
    {
        $content = "";
        
        // post actions
        if (Request::post('action'))
        {
            if (Request::post('csrf') && Security::check(Request::post('csrf')))
            {
                if (Request::post('action') === 'holiedit')
                {
                    $content = self::editHoliday();
                }
                else if((Request::post('action') === 'holidoedit') && Request::post('holi_id'))
                {
                    self::addHoliday(Request::post('name'),
                                     Request::post('day'),
                                     Request::post('month'),
                                     Request::post('type'),
                                     Request::post('holi_id'));
                    $content = self::showHolidays();
                }
                else if((Request::post('action') === 'evtdoedit') && Request::post('evt_id'))
                {
                    $dt = new DateTime(sprintf("%s %s", Request::post('date'), Request::post('time')));
                    self::addEvent(Request::post('what'), 
                                   $dt->format("Y-m-d H:i"),
                                   Request::post('descr'),
                                   Request::post('where'),
                                   Request::post('evt_id'));

                    $content = self::showEvents($dt->format("Y"));
                }
                else if ((Request::post('action') === 'evtshow') && (Request::post('year')))
                {
                    $content = self::showEvents(Request::post('year'));
                }
                else if (Request::post('action') === 'storesettings')
                {
                    Option::update('weekstart', Request::post('weekstart'));
                    Option::update('churchcalendar', Request::post('chcal'));
                }
            }
            else 
            {
                // hacker!
                die('csrf detected!'); 
            }
        } // get actions
        else if (Request::get('action'))
        {
            // delete / edit holiday is done through 'get' ...
            if (Request::get('csrf') && Security::check(Request::get('csrf')))
            {
                if ((Request::get('action') === 'holidel') && Request::get('holi_id'))
                {
                    EventCal::delHoliday(Request::get('holi_id'));
                    $content = self::showHolidays();
                }
                else if ((Request::get('action') === 'holiedit') && Request::get('holi_id'))
                {
                    $content = self::editHoliday(Request::get('holi_id'));
                }
                else if (Request::get('action') === 'holishow')
                {
                    $content = self::showHolidays();
                }
                else if (Request::get('action') === 'evtshow')
                {
                    $year    = Request::get('year') ? Request::get('year') : date("Y");
                    $content = self::showEvents($year);
                }
                else if ((Request::get('action') === 'evtdel') && Request::get('evt_id'))
                {
                    EventCal::delEvent(Request::get('evt_id'));
                    $year    = Request::get('year') ? Request::get('year') : date("Y");
                    $content = self::showEvents($year);
                }
                else if ((Request::get('action') === 'evtedit') && Request::get('evt_id'))
                {
                    $content = self::editEvent(Request::get('evt_id'));
                }
                else if(Request::get('action') === 'cal_settings')
                {
                    $content = self::editSettings();
                }
            }
            else 
            { 
                // hacker!
                die('csrf detected!'); 
            }
        }
        
        echo self::makeHead().$content;
    }
    
    /**
     * show all holidays
     * @return string
     */
    private static function showHolidays ()
    {
        $holidays = EventCal::holidays();
        return View::factory('calendar/views/backend/holidays')
                ->assign('holidays', $holidays)
                ->assign('token', Security::token())
                ->render();
    }
    
    /**
     * show events for given year
     * @param int $year
     * @return string
     */
    private static function showEvents ($year)
    {
        self::checkEventCal($year);
        $events = self::$maincal->events();
        $years  = EventCal::allYears();

        return View::factory('calendar/views/backend/events')
                ->assign('events', $events)
                ->assign('year', $year)
                ->assign('years', $years)
                ->assign('token', Security::token())
                ->render();
    }
    
    /**
     * make holiday edit form
     * @param int $id
     * @return string
     */
    private static function editHoliday ($id = -1)
    {
        $entry = ($id === -1) ? array() : EventCal::holiday($id);
        
        if (($js = File::getContent('../plugins/calendar/js/holiedit.js')) !== FALSE) 
        {
            $js = "<script type='text/javascript'>\n"
                 ."<!--\n".$js."// -->\n"
                 ."</script>\n";
        }

        return View::factory('calendar/views/backend/holiedit')
                ->assign('holiday', $entry[0])
                ->assign('id', $id)
                ->assign('js', $js)
                ->render();
    }
    
    /**
     * make event edit form
     * @param int $id
     * @return string
     */
    private static function editEvent ($id = -1)
    {
        $entry = ($id === -1) ? array() : EventCal::event($id);
        
        if (($js = File::getContent('../plugins/calendar/js/evtedit.js')) !== FALSE) 
        {
            $js = "<script type='text/javascript'>\n"
                 ."<!--\n".$js."// -->\n"
                 ."</script>\n";
        }

        return View::factory('calendar/views/backend/evtedit')
                ->assign('event', $entry[0])
                ->assign('id', $id)
                ->assign('js', $js)
                ->render();
    }
    
    /**
     * add / update holiday
     * @param string $name
     * @param int $day
     * @param int $month
     * @param string $type
     * @param int $id
     */
    private static function addHoliday ($name, $day, $month, $type, $id)
    {
        if ($type === 'fix')
        {
            if ($day && $month && $name)
            {
                EventCal::addFixHoliday($day, $month, $name, $id);
            }
        }
        else if ($type === 'variable')
        {
            if ($day && $name)
            {
                EventCal::addVarHoliday($day, $name, $id);
            }
        }
    }
    
    /**
     * add / update event
     * @param string $name
     * @param string $datetime
     * @param string $descr
     * @param string $where
     * @param int $id
     */
    private static function addEvent ($name, $datetime, $descr, $where, $id)
    {
        if (($name != "") && ($datetime != ""))
        {
            EventCal::addEvent($datetime, $name, $where, $descr, $id);
        }
    }
    
    /**
     * show possible actions
     * @return string
     */
    private static function makeHead()
    {
        return View::factory('calendar/views/backend/actions')
                ->assign('token', Security::token())
                ->render();
    }
    
    private static function editSettings()
    {
        return View::factory('calendar/views/backend/settings')
                ->assign('token', Security::token())
                ->render();
    }
}