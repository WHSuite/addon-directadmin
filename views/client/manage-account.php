<div class="row">
    <div class="col-md-8">
        <h3 class="nomargin"><?php echo $account[0]['domain']; ?></h3>
    </div>
    <div class="col-md-4 text-right">
        <b><?php echo $lang->get('diskspace_mb'); ?>: </b> <?php echo $hosting->diskspace_usage; ?> / <?php echo $hosting->diskspace_limit; ?><br>
        <b><?php echo $lang->get('bandwidth_mb'); ?>: </b> <?php echo $hosting->bandwidth_usage; ?> / <?php echo $hosting->bandwidth_limit; ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="well text-center">
            <a href="http://<?php echo $hosting->domain; ?>:2222/" class="btn btn-primary" target="_blank"><?php echo $lang->get('access_control_panel'); ?></a>
        </div>
    </div>
</div>
