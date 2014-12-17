<h3><?php echo __('Edit / Add Holiday', 'eventcal'); ?></h3>
<form action="" method="post">
    <input type="hidden" name="csrf" value="<?php echo Security::token(); ?>" />
    <input type="hidden" name="action" value="holidoedit" />
    <input type="hidden" name="holi_id" value="<?php echo $id; ?>" />
    <table class='table table-bordered'>
        <tbody>
            <tr>
                <td><label for='name'><?php echo __('Name', 'eventcal'); ?>:<label></td>
                <td><input type='text' id='name' name='name' value='<?php echo Arr::get($holiday, 'name'); ?>' /></td>
            </tr>
            <tr>
                <td><label for='type'><?php echo __('Type', 'eventcal'); ?>:<label></td>
                <td>
                    <select id='type' name='type' onchange='enaDisa(this);'>
                        <option value="fix"<?php echo (Arr::get($holiday, 'fix') === 'yes') ? " selected='selected'" : ""; ?>><?php echo __('fix', 'eventcal'); ?></option>
                        <option value="variable"<?php echo (Arr::get($holiday, 'fix') === 'no') ? " selected='selected'" : ""; ?>><?php echo __('variable', 'eventcal'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for='day'><?php echo __('Day', 'eventcal'); ?>:<label></td>
                <td><input type='text' id='day' name='day' value='<?php echo Arr::get($holiday, 'day'); ?>' /></td>
            </tr>
            <tr>
                <td><label for='month'><?php echo __('Month', 'eventcal'); ?>:<label></td>
                <td><input type='text' id='month' name='month' value='<?php echo Arr::get($holiday, 'month'); ?>' /></td>
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