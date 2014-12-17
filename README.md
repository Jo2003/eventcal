# Event Calendar Plugin for Monstra
&copy; 2014 by [Jo2003](http://www.coujo.de)

## Features
This event calendar supports 2 kinds of holidays:

1. Fixed holidays (based on a fixed date)
2. Variable holidays (based on an offset to Easter Sunday)

The holidays and events you add are displayed in the calendar table as well as in the
event overview.

## Post Installation
There is a CSS style sheet which you should include into your site.  
It is located in:  

    plugins/calendar/css/frontend/calendar.css

To include this CSS file add following line to your themes header template where all
the other style sheets are included:

    <?php Stylesheet::add('plugins/calendar/css/frontend/calendar.css', 'frontend', 4); ?>

After inserting this line please delete temporary files:  
    
    System -> Settings -> Delete Temporary Files

## Usage  
### Slug
Once installed the calendar can be accessed through *slug* "calendar".  

    http://your.monstra.site/calendar

### ShortCode
You can add a month table to your site using following short code:  

    {thisMonth}

This will create a table which shows current month.  
Three optional parameters are supported: *year*, *month*, *link*:

    {thisMonth year=2013 month=6 link='http://google.com'}

This creates a month table for June 2013 with headlink set to Google.

### PHP Code
The related php code would like like follows:

    <?php echo Calendar::monthTab(); ?>
    <?php echo Calendar::monthTab(array('year' => 2013, 'month' => 6, 'link' => 'http://google.com')); ?>

## Administration  
This plugin is fully administrable through the admin panel of Monstra.  
You can reach the calendar admin site through *Dashboard* or *Content -> Calendar*.

### General Settings
In *General Settings* you can choose if the week should start on Monday or Sunday. 
Furthermore you can choose if your Easter date is based on the latin church or the
orthodox church (Easter day in orthodox church may vary to the date of the latin church).

### Add / Edit Holiday
Here you can add / edit / delete holidays. Please note that the Easter based holidays 
are marked with *variable*. For these holidays the month has no meaning. In day
you have to insert the offset to the Easter Sunday in number of days.  
E.g. an offset of 0 days means Easter Sunday, an offset of -2 days would be 
"Good Friday" and an offset of 49 days would be "Pentecost Sunday".  

### Add / Edit Event
Here you can add / edit / delete your events. For events you should at least insert 
the date and the name of the event. Other fields are optionally.  
The description field supports the [MarkDown](http://daringfireball.net/projects/markdown/syntax) 
symtax (if the related plugin is installed).



