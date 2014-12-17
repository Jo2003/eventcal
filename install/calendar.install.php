<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

require_once PLUGINS.DS.'calendar'.DS.'engine'.DS.'eventcal.php';

Option::add('weekstart', 1);
Option::add('churchcalendar', 'latin');
                    
EventCal::createTables();

EventCal::addFixHoliday(1 , 1 , "New Year's Day");
EventCal::addVarHoliday(-2, "Good Friday");
EventCal::addVarHoliday( 0, "Easter Sunday");
EventCal::addVarHoliday( 1, "Easter Monday");
