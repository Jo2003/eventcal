<h3><?php echo __('General Settings', 'eventcal'); ?></h3>
<form action="" method="post">
    <input type="hidden" name="csrf" value="<?php echo $token; ?>" />
    <input type="hidden" name="action" value="storesettings" />
    <table class='table table-bordered'>
        <tbody>
            <tr>
                <td><label for='weekstart'><?php echo __('Week start', 'eventcal'); ?>:<label></td>
                <td>
                    <select name="weekstart" id="weekstart">
<?php
$opts = "<option value='1'";
if ((int)Option::get('weekstart') === 1)
{
    $opts .= " selected='selected'";
}
$opts .= ">".__('Monday', 'eventcal')."</option>\n"
        ."<option value='7'";
if ((int)Option::get('weekstart') === 7)
{
    $opts .= " selected='selected'";
}
$opts .= ">".__('Sunday', 'eventcal')."</option>\n";
echo $opts;
?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for='chcal'><?php echo __('Church Calendar', 'eventcal'); ?>:<label></td>
                <td>
                    <select name="chcal" id="chcal">
<?php
$opts = "<option value='latin'";
if (Option::get('churchcalendar') === 'latin')
{
    $opts .= " selected='selected'";
}
$opts .= ">".__('Latin', 'eventcal')."</option>\n"
        ."<option value='orthodox'";
if (Option::get('churchcalendar') === 'orthodox')
{
    $opts .= " selected='selected'";
}
$opts .= ">".__('Orthodox', 'eventcal')."</option>\n";
echo $opts;
?>                        
                    </select>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type='reset' name="reset" value="<?php echo __('Reset', 'eventcal'); ?>" class='btn btn-phone btn-primary' />&nbsp;&nbsp;&nbsp;
                    <input type='submit' name="submit" value="<?php echo __('Submit', 'eventcal'); ?>" class='btn btn-phone btn-primary' />
                </td>
            </tr>
        </tbody>
    </table>
</form>