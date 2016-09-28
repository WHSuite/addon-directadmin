<div class="row">
    <div class="col-md-12">
        <h3 class="nomargin"><?php echo $account[0]['domain']; ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="well text-center">
            <a href="<?php echo $router->generate('admin-service-directadmin-create', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-secondary">Create Account</a>
            <a href="<?php echo $router->generate('admin-service-directadmin-suspend', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-warning">Suspend Account</a>
            <a href="<?php echo $router->generate('admin-service-directadmin-unsuspend', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-warning">Unsuspend Account</a>
            <a href="<?php echo $router->generate('admin-service-directadmin-terminate', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-danger" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>')">Terminate Account</a>
        </div>
    </div>
</div>
