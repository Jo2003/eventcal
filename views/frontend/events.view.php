<?php
$cont = "<table class='cal-layout'>\n";

foreach ($months as $month) 
{
    $cont .= "<tr>\n<td colspan='4'>\n<br />\n"
            ."<span class='flright'>[<a href='#header' title='".__('up', 'eventcal')."'> <strong>&uarr;</strong> </a>]</span>\n"
            ."<div class='pseudoh3' id='".$month['name']."'>".__($month['name'], 'eventcal')."</div>\n"
            ."</td>\n</tr>\n<tr>\n"
            ."<th>".__('Date' , 'eventcal')."</th>\n"
            ."<th>".__('Time' , 'eventcal')."</th>\n"
            ."<th>".__('What' , 'eventcal')."</th>\n"
            ."<th>".__('Where', 'eventcal')."</th>\n"
            ."</tr>\n";
    
    if (count($month['events']) == 0)
    {
        $cont .= "<tr>\n<td class='evtdescr' colspan='4'>"
                .__('So far no events for this month.', 'eventcal')
                ."</td>\n</tr>\n";
    }
    else
    {
        foreach ($month['events'] as $evtdate => $events) 
        {
            $rowspan = (count($events) > 1) ? " rowspan='".count($events)."'" : "";
            $id      = new DateTime($evtdate);
            $i       = 0;
            
            foreach ($events as $event)
            {
                $cont .= "<tr>\n";
                if ($i++ === 0)
                {
                    $cont .= "<td class='evtdescr text-nowrap' id='id".$id->format("Ymd")."'".$rowspan.">"
                            .__($event['wday'], 'eventcal').",<br />"
                            .$event['date'];
                    
                    if ($event['holiday'] !== FALSE)
                    {
                        $cont .= "<br /><span class='holiday'>(".$event['holiday'].")</span>";
                    }
                    
                    $cont .= "</td>\n";
                }
                
                $cont .= "<td class='evtdescr'>".(($event['time'] === '00:00') ? "&nbsp;" : $event['time'])."</td>\n"
                        ."<td class='events'><strong>".$event['name']."</strong>";

                if ($event['descr'])
                {
                    $cont .= "<br />".Filter::apply('content', $event['descr']);
                }

                $cont .= "</td>\n"
                        ."<td class='evtdescr'>".$event['where']."</td>\n"
                        ."</tr>\n";
            }
        }
    }
}
$cont .= "</table>";
echo $cont;