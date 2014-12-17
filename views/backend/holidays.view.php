<h3 class='text-center'><?php echo __('Holidays', 'eventcal'); ?></h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th><?php echo __('Holiday', 'eventcal'); ?></th>
            <th><?php echo __('Day / Offset', 'eventcal'); ?></th>
            <th><?php echo __('Month', 'eventcal'); ?></th>
            <th><?php echo __('Type', 'eventcal'); ?></th>
            <th><?php echo __('Actions', 'eventcal'); ?></th>
        </tr>
    </thead>
<?php if (count($holidays)) { ?>
    <tbody>
<?php foreach($holidays as $holiday) { ?>
        <tr>
            <td><?php echo $holiday['name']; ?></td>
            <td><?php echo $holiday['day']; ?></td>
            <td><?php echo ($holiday['month'] != -1) ? $holiday['month'] : "---"; ?></td>
            <td><?php echo ($holiday['fix'] === 'yes') ? __('fix', 'eventcal') : __('variable', 'eventcal'); ?></td>
            <td>
<?php
            echo Html::anchor(__('Edit', 'eventcal'), 
                    'index.php?id=calendar&action=holiedit&holi_id='.$holiday['id'].'&csrf='.$token)
                .Html::nbsp(3)."\n"
                .Html::anchor(__('Delete', 'eventcal'), 
                        'index.php?id=calendar&action=holidel&holi_id='.$holiday['id'].'&csrf='.$token, 
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
            return confirm('<?php echo __('Are you sure you want to delete this holiday entry?', 'eventcal'); ?>');
        }
    );
</script>