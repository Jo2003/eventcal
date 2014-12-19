<?php require_once PLUGINS.DS.'calendar'.DS.'engine'.DS.'eventcal.php';

// Register plugin
Plugin::register( __FILE__,
                __('Event Calendar!', 'eventcal'),
                __('A simple event calendar for Monstra', 'eventcal'),
                '1.0.0',
                'Jo2003',
                'http://www.coujo.de/',
                'calendar');

Shortcode::add("thisMonth", "Calendar::monthTab");

// Load Calendar Admin for Editor and Admin
if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) 
{
    Plugin::admin('calendar');
}


/**
 * calendar plugin class derived from Frontend
 */
class Calendar extends Frontend
{
    /**
     * main calendar
     * @var EventCal
     */
    private static $maincal  = null;
    
    /**
     * english week days (short)
     * @var type array 
     */
    private static $dayNames = array('Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su');
    
    /**
     * collects content to display
     * @var string content buffer
     */
    private static $ecalcontent = '';
    
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
     * Calendar main function
     */
    public static function main()
    {
        $year = date("Y");
        
        if (Request::post('ecal_year'))
        {
            $year = Request::post('ecal_year');
        }
        else if (Request::get('ecal_year'))
        {
            $year = Request::get('ecal_year');
        }
        
        self::$ecalcontent = self::yearCalendar(array('year' => $year));
        self::$ecalcontent.= self::yearChooser(array('year' => $year));
        self::$ecalcontent.= self::yearEvents(array('year' => $year));
    }
    
    /**
     * create a month table
     * @param array $params
     * @return string
     */
    public static function monthTab($params = array())
    {
        $l  = Arr::get($params, 'link')      ? Arr::get($params, 'link')      : "";
        $y  = Arr::get($params, 'year')      ? Arr::get($params, 'year')      : date("Y");
        $m  = Arr::get($params, 'month')     ? Arr::get($params, 'month')     : date("m");
        
        return self::createMonthTab($m, $y, $l);
    }
    
    /**
     * create html code for one month table
     * @param int $month 1 ... 12
     * @param int $year optional 
     * @param string $headLink optional link to be placed in table header
     * @return string html code
     */
    private static function createMonthTab($month, $year, $headLink)
    {
        $tab       = "";
        $skel      = "";
        $mname     = "";
        $dstart    = 0;
        $weekstart = (int)Option::get('weekstart');
        
        self::checkEventCal($year);
        $ma = self::$maincal->monthArray($month);
        
        if (($days = count($ma)) > 0)
        {
            $dt         = new DateTime($ma[0]['date']);
            $mname      = __($dt->format("F"), 'eventcal');
            $dstart     = $ma[0]['wday'];
            $colspanin  =  0;
            $colspanout =  0;
            $weekend    = ($weekstart == 7) ? 6 : 7;

            // ---------------------------------------------------------
            // initial colspan ...
            if ($dstart != $weekstart)
            {
                $colspanin = ($weekstart == 7) ? $dstart : ($dstart - 1);
                $tab       = "<tr>\n<td";
                
                if ($colspanin > 1)
                {
                    $tab .= " colspan='".$colspanin."'";
                }
                
                $tab .= ">".Html::nbsp()."</td>\n";
            }

            // ---------------------------------------------------------
            // table columns and rows ...
            for ($i = 0; $i < $days; $i++)
            {
                $link = "";
                
                // open new row?
                if ($ma[$i]['wday'] == $weekstart)
                {
                    $tab .= "<tr>\n";
                }

                // content ..
                $tab .= "<td";
                
                $css = array();

                if ($ma[$i]['holiday'] !== FALSE)
                {
                    $css[] = 'holiday';
                }

                if (count($ma[$i]['events']) > 0)
                {
                    $css[] = 'event';
                    $link = "<a href='"
                            .Option::get('siteurl')
                            ."/calendar?ecal_year=".$year."#id"
                            .str_replace("-", "", $ma[$i]['date'])."'>";
                }
                
                if (count($css) > 0)
                {
                    $tab .= " class='".implode(" ", $css)."'";
                }

                $tab .= ">";
                
                if ($link !== "")
                {
                    $tab .= $link;
                }
                
                $tab .= $ma[$i]['mday'];
                
                if ($link !== "")
                {
                    $tab .= "</a>";
                }
                
                $tab .= "</td>\n";

                if ($ma[$i]['wday'] == $weekend)
                {
                    $tab .= "</tr>\n";
                }
            }

            // ---------------------------------------------------------
            // ending colspan ...
            $colspanout = 7 - (($days - (7 - $colspanin)) % 7);
            
            if (($colspanout > 0) && (($colspanout < 7)))
            {
                $tab .= "<td";
                
                if ($colspanout > 1)
                {
                    $tab .= " colspan='".$colspanout."'";
                } 
               
                $tab .= ">".Html::nbsp()."</td>\n</tr>\n";
            }

            // creating table ...
            $skel = "<table class='month'>\n"
                   ."<tr>\n<th class='center' colspan='7'>";

            if ($headLink != '')
            {
                $skel .= "<a href='".$headLink."'>".$mname."</a>";
            }
            else
            {
                $skel .= $mname;
            }

            $skel .= "</th>\n</tr>\n<tr>\n";

            if ($weekstart == 7)
            {
                $skel .= "<td class='sun bold'>".__(self::$dayNames[6], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[0], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[1], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[2], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[3], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[4], 'eventcal')."</td>\n"
                        ."<td class='sat bold'>".__(self::$dayNames[5], 'eventcal')."</td>\n";
            }
            else
            {
                $skel .= "<td class='bold'>".__(self::$dayNames[0], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[1], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[2], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[3], 'eventcal')."</td>\n"
                        ."<td class='bold'>".__(self::$dayNames[4], 'eventcal')."</td>\n"
                        ."<td class='sat bold'>".__(self::$dayNames[5], 'eventcal')."</td>\n"
                        ."<td class='sun bold'>".__(self::$dayNames[6], 'eventcal')."</td>\n";
            }

            $skel .= "</tr>\n".$tab."</table>\n";
        }

        return $skel;
    }

    /**
     * create calendar of choosen year
     * @param array $params
     * @return string
     */
    public static function yearCalendar($params = array())
    {
        $y      = Arr::get($params, 'year') ? Arr::get($params, 'year') : date("Y");
        $months = array();
        
        for ($i = 1; $i <= 12; $i++)
        {
            $mdt        = new DateTime(sprintf("%d-%.02d-01", $y, $i));
            $months[$i] = self::monthTab(array('year' => $y, 'month' => $i, 'link' => '#'.$mdt->format("F")));
        }
        
        return View::factory('calendar/views/frontend/year')
                ->assign('year', $y)
                ->assign('months', $months)
                ->render();
    }
    
    /**
     * create a list(table) of events of this year
     * @param array $params
     * @return string
     */
    public static function yearEvents($params = array())
    {
        $y      = Arr::get($params, 'year') ? Arr::get($params, 'year') : date("Y");
        $months = array();
        
        self::checkEventCal($y);

        for ($i = 1; $i <= 12; $i++)
        {
            $ma     = self::$maincal->monthArray($i);
            $events = array();
            $m      = new DateTime(sprintf("%d-%.02d-01", $y, $i));
            $mname  = $m->format("F");
            
            foreach($ma as $day)
            {
                if (count($day['events']) > 0)
                {
                    $m = new DateTime($day['date']);
                    foreach($day['events'] as $event)
                    {
                        $events[$day['date']][] = array(
                            'wday'    => $m->format("l")     ,
                            'date'    => $m->format("d.m.Y") ,
                            'time'    => $event['time']      ,
                            'holiday' => $day['holiday']     ,
                            'name'    => $event['name']      ,
                            'descr'   => $event['descr']     ,
                            'where'   => $event['where']
                        );
                    }
                }
            }

            $months[$i] = array(
                'name'   => $mname,
                'events' => $events
            );
        }
        
        return View::factory("calendar/views/frontend/events")
                ->assign('months', $months)
                ->render();
    }
    
    /**
     * make year chooser form
     * @param array $params
     * @return string
     */
    public static function yearChooser($params = array())
    {
        $y          = Arr::get($params, 'year') ? Arr::get($params, 'year') : date("Y");
        $years      = EventCal::allYears();
        $actionlink = Option::get('siteurl')."/calendar?ecal_year=".$y;
        
        return View::factory("calendar/views/frontend/yearselect")
                ->assign('years'     , $years)
                ->assign('year'      , $y)
                ->assign('actionlink', $actionlink)
                ->render();
    }

    /**
     * return page title
     * @return string
     */
    public static function title()
    {
        return __('Event Calendar!', 'eventcal');
    }

    /**
     * returns collected content
     * @return string
     */
    public static function content() 
    {
        return self::$ecalcontent;
    }
}
