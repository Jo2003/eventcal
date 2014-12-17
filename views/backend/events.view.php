<h3 class='text-center'><?php echo __('Events', 'eventcal'); ?></h3>
<div class='container text-center'>
    <form action="" method="post">
        <input type="hidden" name="csrf" id="csrf" value="<?php echo $token; ?>" />
        <input type="hidden" name="action" id="action" value="evtshow" />
        <label for="year"><?php echo __('Choose a year', 'eventcal'); ?>:</label>
        <select name="year" id="year" onchange='this.form.submit();'>
<?php
foreach(array_keys($years) as $y) {
    $selected = ((int)$year === (int)$y) ? " selected='selected'" : "";
    echo "<option value='".$y."'".$selected.">".$y."</option>\n";
} ?>
        </select>
    </form>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Date', 'eventcal'); ?></th>
            <th><?php echo __('Time', 'eventcal'); ?></th>
            <th><?php echo __('What', 'eventcal'); ?></th>
            <th><?php echo __('Where', 'eventcal'); ?></th>
            <th><?php echo __('Actions', 'eventcal'); ?></th>
        </tr>
    </thead>
<?php if (count($events)) { ?>
    <tbody>
<?php foreach($events as $event) { ?>
        <tr>
            <td><span style='white-space: nowrap;'><?php echo $event['date']; ?></span></td>
            <td><?php echo $event['time']; ?></td>
            <td>
<?php 
echo "<strong>".$event['name']."</strong>";
if ($event['descr'] != '')
{
    echo "<br /><span>".Filter::apply('content', $event['descr'])."</span>\n";
}
?>          </td>
            <td><?php echo $event['where']; ?></td>
            <td>
<?php
            echo Html::anchor(__('Edit', 'eventcal'), 
                    'index.php?id=calendar&action=evtedit&evt_id='.$event['id'].'&year='.$year.'&csrf='.$token)
                .Html::nbsp(3)."\n"
                .Html::anchor(__('Delete', 'eventcal'), 
                        'index.php?id=calendar&action=evtdel&evt_id='.$event['id'].'&year='.$year.'&csrf='.$token, 
                        array('class' => 'confirmation'))."\n";
?>
            </td>
        </tr>
<?php } ?>
    </tbody>
<?php } ?>
</table>
<script type="text/javascript">
    $('.confirmation').on('click', 
        function () 
        {
            return confirm('<?php echo __('Are you sure you want to delete this event?', 'eventcal'); ?>');
        }
    );
</script>