<h3><?php echo __('Edit / Add Event', 'eventcal'); ?></h3>
<form action="" method="post">
    <input type="hidden" name="csrf" value="<?php echo Security::token(); ?>" />
    <input type="hidden" name="action" value="evtdoedit" />
    <input type="hidden" name="evt_id" value="<?php echo $id; ?>" />
    <table class='table table-bordered'>
        <tbody>
            <tr>
                <td><label for='date'><?php echo __('Date', 'eventcal'); ?>:<label></td>
                <td>
                    <input type='text' id='date' name='date' value='<?php echo Arr::get($event, 'date'); ?>' />
                    <?php echo "(".__('e.g.', 'eventcal')." 2014-12-24)"; ?>
                </td>
            </tr>
            <tr>
                <td><label for='time'><?php echo __('Time', 'eventcal'); ?>:<label></td>
                <td>
                    <input type='text' id='time' name='time' value='<?php echo Arr::get($event, 'time'); ?>' />
                    <?php echo "(".__('e.g.', 'eventcal')." 19:30)"; ?>
                </td>
            </tr>
            <tr>
                <td><label for='what'><?php echo __('What', 'eventcal'); ?>:<label></td>
                <td><input type='text' id='what' name='what' value='<?php echo Arr::get($event, 'name'); ?>' /></td>
            </tr>
            <tr>
                <td><label for='descr'><?php echo __('Description', 'eventcal'); ?>:<label></td>
                <td>
                    <textarea name='descr' id='descr' rows='5' cols='80'><?php echo Arr::get($event, 'descr'); ?></textarea>
                    <div><a href='http://daringfireball.net/projects/markdown/syntax' target='_blank'><?php echo __('MarkDown synthax supported.', 'eventcal'); ?></a></div>
                </td>
            </tr>
            <tr>
                <td><label for='where'><?php echo __('Where', 'eventcal'); ?>:<label></td>
                <td><input type='text' id='where' name='where' value='<?php echo Arr::get($event, 'where'); ?>' /></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type='reset' name="reset" value="<?php echo __('Reset', 'eventcal'); ?>" onclick='resetColor(this);' class='btn btn-phone btn-primary' />&nbsp;&nbsp;&nbsp;
                    <input type='submit' name="submit" value="<?php echo __('Submit', 'eventcal'); ?>" onclick='return checkForm(this, "<?php echo __('Please check your input!', 'eventcal')." ".__('Missing fields are marked red.', 'eventcal'); ?>");' class='btn btn-phone btn-primary' />
                </td>
            </tr>
        </tbody>
    </table>
</form>
<?php
echo $js;