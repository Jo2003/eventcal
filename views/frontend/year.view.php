<?php
$cont = "<h3 class='center' id='top'>".__('Events for the year', 'eventcal')." ".$year."</h3>\n"
       ."<table class='cal-layout'>\n";

foreach ($months as $m => $mtab) 
{
    if (($m % 3) == 1)
    {
        $cont .= "<tr>\n";
    }
    
    $cont .= "<td class='yearcal'>\n".$mtab."</td>\n";
    
    if (($m % 3) == 0)
    {
        $cont .= "</tr>\n";
    }
}
$cont .= "</table>";

echo $cont;