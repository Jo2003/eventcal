<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

require_once PLUGINS.DS.'calendar'.DS."engine".DS."eventcal.php";
EventCal::dropTables();
Option::delete('weekstart');
Option::delete('churchcalendar');
