<div class="row">
    <div class="col-md-2">
        <img src="<?php echo $assets->image('Directadmin::logo.png'); ?>" width="100%">
    </div>
    <div class="col-md-1 text-right">
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <?php echo $lang->get('options'); ?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo $router->generate('admin-server-directadmin-restart-service', array('id' => $group->id, 'server_id' => $server_id, 'service' => 'httpd')); ?>"><?php echo $lang->get('restart_httpd'); ?></a></li>
                <li><a href="<?php echo $router->generate('admin-server-directadmin-restart-service', array('id' => $group->id, 'server_id' => $server_id, 'service' => 'mysqld')); ?>"><?php echo $lang->get('restart_mysql'); ?></a></li>
                <li><a href="<?php echo $router->generate('admin-server-directadmin-restart-service', array('id' => $group->id, 'server_id' => $server_id, 'service' => 'directadmin')); ?>"><?php echo $lang->get('directadmin_restart_directadmin'); ?></a></li>
                <li class="divider"></li>
                <li><a href="<?php echo $router->generate('admin-server-directadmin-reboot', array('id' => $group->id, 'server_id' => $server_id)); ?>"><?php echo $lang->get('reboot_server'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-9 text-right">

        <h2 class="nomargin"><?php echo $server['ip']; ?></h2>
    </div>

</div>

