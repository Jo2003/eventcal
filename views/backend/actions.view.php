<h2 class='text-center'><?php echo __('Calendar Admin', 'eventcal'); ?></h2><br />
<div class='text-center'>
    <a href='index.php?id=calendar&amp;action=cal_settings&amp;csrf=<?php echo $token; ?>' class='btn btn-phone btn-default'><?php echo __('General Settings', 'eventcal'); ?></a>&nbsp;&nbsp;&nbsp;
    <a href='index.php?id=calendar&amp;action=evtedit&amp;evt_id=-1&amp;csrf=<?php echo $token; ?>' class='btn btn-phone btn-default'><?php echo __('New Event', 'eventcal'); ?></a>&nbsp;&nbsp;&nbsp;
    <a href='index.php?id=calendar&amp;action=holiedit&amp;holi_id=-1&amp;csrf=<?php echo $token; ?>' class='btn btn-phone btn-default'><?php echo __('New Holiday', 'eventcal'); ?></a>&nbsp;&nbsp;&nbsp;
    <a href='index.php?id=calendar&amp;action=evtshow&amp;csrf=<?php echo $token; ?>' class='btn btn-phone btn-default'><?php echo __('Edit Event', 'eventcal'); ?></a>&nbsp;&nbsp;&nbsp;
    <a href='index.php?id=calendar&amp;action=holishow&amp;csrf=<?php echo $token; ?>' class='btn btn-phone btn-default'><?php echo __('Edit Holiday', 'eventcal'); ?></a>
</div><br /><hr />
