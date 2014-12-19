<?php

$cont = "<div class='roundedbox'>\n"
       ."<form method='post' action='".$actionlink."'>\n"
       ."<label for='ecal_year'>".__('Choose a year', 'eventcal').":</label>".Html::nbsp()."\n"
       ."<select name='ecal_year' id='ecal_year' onchange='this.form.submit()'>\n";

foreach(array_keys($years) as $y)
{
    $cont .= "<option value='".$y."'".(($y == $year) ? " selected='selected'" : "").">".$y."</option>\n";
} 
$cont .= "</select>\n</form>\n</div>";
echo $cont;
