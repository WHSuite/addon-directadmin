<?php
namespace Addon\Directadmin\Libraries;
use Addon\Directadmin\Libraries\Api\Directadmin as API;

class Directadmin
{
    public $server;
    public $server_group;
    public $server_module;

    public $hosting;
    public $cmd;

    public function initServer($server, $server_group, $server_module) {

        $this->server = $server;
        $this->server_group = $server_group;
        $this->server_module = $server_module;
    }

    public function updateRemote($purchase_id)
    {
        // Load the account and server details
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();

        if (! empty($hosting) && $this->retrieveService($hosting->id)) {

            if ($hosting->last_sync < (time()-3600)) {
                $this->loadAccount($hosting->id);

                $service = $this->retrieveService($hosting->id);
            }
            return true;
        }
    }

    public function addAddon($product_addon_id, $addon_purchase_id, $purchase_id)
    {
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();

        $this->loadAccount($hosting->id);

        return true;
    }

    public function updateAddon($product_addon_id, $addon_purchase_id, $purchase_id)
    {
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();

        $this->loadAccount($hosting->id);

        return true;
    }

    public function deleteAddon($product_addon_id, $addon_purchase_id, $purchase_id)
    {
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();

        $this->loadAccount($hosting->id);

        return true;
    }

    private function loadAccount($hosting_id)
    {
        // Load the hosting package
        $this->hosting = \Hosting::find($hosting_id);

        // Load the server
        $this->server = $this->hosting->Server()->first();

        // Load the server group
        $this->server_group = $this->server->ServerGroup()->first();
    }

    public function testConnection($server_data)
    {
        // Check the correct details have been provided.
        if (!isset($server_data['Server']['main_ip']) || $server_data['Server']['main_ip'] == '' ||
            !isset($server_data['Server']['username']) || $server_data['Server']['username'] == '' ||
            !isset($server_data['Server']['password']) || $server_data['Server']['password'] == '') {
            return false;
        }

        try {

            $api = new API($server_data['Server']['main_ip'], '2222', $server_data['Server']['username'], $server_data['Server']['password'], $server_data['Server']['ssl_connection']);

            $response = $api->get('/CMD_API_SYSTEM_INFO');

            // The response from DirectAdmin should contain the basic server details.
            // If it doesn't we've not got a valid response, and will therefore return
            // a failed status.

            // For this test we'll check something that every copy of DirectAdmin should
            // be able to return, and thats the uptime variable. As a fallback, we'll also
            // offer the option of checking the numcpus variable.
            if (isset($response['uptime']) || isset($response['numcpus'])) {
                // Returned a server connection successfully!
                return true;
            }
            return false;

        } catch (\Exception $e) {

            // An exception was thrown by an invaid request. The connection to the server
            // can't be valid, so return false.
            return false;
        }
        return false;
    }

    public function serverConnection()
    {
        $this->cmd = new API($this->server->main_ip, '2222', \App::get('security')->decrypt($this->server->username), \App::get('security')->decrypt($this->server->password), $this->server->ssl_connection);
    }

    public function productFields()
    {
        // Get server details
        $this->serverConnection();

        $forms = \App::factory('\Whsuite\Forms\Forms');

        $form = '';

        $package_list = array();

        $shared_packages = $this->cmd->get('/CMD_API_PACKAGES_USER');
        if (isset($shared_packages['list']) && ! empty($shared_packages['list'])) {
            foreach ($shared_packages['list'] as $plan) {
                $package_list[$plan] = $plan.' (SHARED)';
            }
        }

        $this->serverConnection();

        $reseller_packages = $this->cmd->get('/CMD_API_PACKAGES_RESELLER');
        if (isset($reseller_packages['list']) && ! empty($reseller_packages['list'])) {
            foreach ($reseller_packages['list'] as $id => $plan) {
                $package_list[$plan] = $plan.' (RESELLER)';
            }
        }

        $form .= $forms->select('PackageMeta.directadmin_package_name', \App::get('translation')->get('package'), array('options' => $package_list));
        $form .= $forms->checkbox('PackageMeta.directadmin_package_is_reseller', \App::get('translation')->get('is_reseller'));
        echo $form;
    }


    public function productPaid($item)
    {
        return;
    }

    public function createService($purchase, $hosting)
    {
        $product = $purchase->Product()->first();
        $product_data = $product->ProductData()->get();
        $client = $purchase->Client()->first();

        $service_fields = array();

        foreach ($product_data as $p_data) {
            $service_fields[$p_data->slug] = $p_data->value;
        }

        $security = \App::get('security');

        if ($product->included_ips != '0') {
            $ip = $product->included_ips = '1';
        } else {
            $ip = '0';
        }

        $package_data = array(
            'package' => $service_fields['directadmin_package_name']
        );

        if (isset($service_fields['directadmin_package_is_reseller']) && $service_fields['directadmin_package_is_reseller'] == '1') {

            $this->serverConnection();
            $package = $this->cmd->get('/CMD_API_PACKAGES_RESELLER', $package_data);

            $account_data = array(
                'action' => 'create',
                'add' => 'Submit',
                'username' => $this->generateUsername($hosting->domain),
                'email' => $client->email,
                'passwd' => $security->decrypt($hosting->password),
                'passwd2' => $security->decrypt($hosting->password),
                'domain' => $hosting->domain,
                'package' => $service_fields['directadmin_package_name'],
                'ip' => 'shared',
                'notify' => 'no'
            );
            $this->serverConnection();
            $create = $this->cmd->post('/CMD_ACCOUNT_RESELLER', $account_data);

        } else {

            $this->serverConnection();
            $package = $this->cmd->get('/CMD_API_PACKAGES_USER', $package_data);

            $account_data = array(
                'action' => 'create',
                'add' => 'Submit',
                'username' => $this->generateUsername($hosting->domain),
                'email' => $client->email,
                'passwd' => $security->decrypt($hosting->password),
                'passwd2' => $security->decrypt($hosting->password),
                'domain' => $hosting->domain,
                'package' => $service_fields['directadmin_package_name'],
                'ip' => $this->server->main_ip,
                'notify' => 'no'
            );
            $this->serverConnection();
            $create = $this->cmd->post('/CMD_API_ACCOUNT_USER', $account_data);
        }

        if (isset($create['error']) && $create['error'] == '0') {

            $diskspace_limit = 0;
            $bandwidth_limit = 0;

            if (isset($package['quota'])) {
                $diskspace_limit = $package['quota'];
                $bandwidth_limit = $package['bandwidth'];
            }

            $hosting->username = $account_data['username'];
            $hosting->diskspace_limit = $diskspace_limit;
            $hosting->bandwidth_limit = $bandwidth_limit;
            $hosting->save();

            $purchase->status = '1';
            $purchase->save();

            $hosting_data = array(
                'domain' => $hosting->domain,
                'nameservers' => $this->server->nameservers,
                'diskspace_limit' => $diskspace_limit,
                'diskspace_usage' => '0',
                'bandwidth_limit' => $bandwidth_limit,
                'bandwidth_usage' => '0',
                'status' => '1',
                'username' => $account_data['username'],
                'password' => $security->decrypt($hosting->password)
            );

            return $hosting_data;
        }
        return false;
    }

    public function renewService($hosting_id)
    {
        // DirectAdmin accounts dont need to do anything here.
        return true;
    }

    public function terminateService($purchase, $hosting = null)
    {
        $this->serverConnection();

        if (! $hosting) {
            $hosting = \Hosting::where('product_purchase_id', '=', $purchase)->first();
        }
        $data = array(
            'confirmed' => 'Confirm',
            'delete' => 'yes',
            'select0' => $hosting->username
        );
        $terminate = $this->cmd->post('/CMD_API_SELECT_USERS', $data);

        if (isset($terminate['error']) && $terminate['error'] == '0') {

            $purchase->status = '3';
            $purchase->save();

            return true;
        }
        return false;
    }

    public function suspendService($purchase, $hosting)
    {

        $this->serverConnection();

        $data = array(
            'location' => 'CMD_SELECT_USERS',
            'confirmed' => 'Confirm',
            'suspend' => 'Suspend',
            'select0' => $hosting->username
        );

        $suspend = $this->cmd->post('/CMD_API_SELECT_USERS', $data);

        if (isset($suspend['error']) && $suspend['error'] == '0') {
            return true;
        }

        return false;
    }

    public function unsuspendService($purchase, $hosting)
    {
        $this->serverConnection();

        $data = array(
            'location' => 'CMD_SELECT_USERS',
            'confirmed' => 'Confirm',
            'suspend' => 'Unsuspend',
            'select0' => $hosting->username
        );

        $unsuspend = $this->cmd->post('/CMD_API_SELECT_USERS', $data);

        if (isset($unsuspend['error']) && $unsuspend['error'] == '0') {
            return true;
        }
        return false;
    }

    public function retrieveService($hosting_id)
    {
        $hosting = \Hosting::find($hosting_id);
        $this->serverConnection($hosting->server_id);

        $account = $this->cmd->get('/CMD_API_SHOW_USER_DOMAINS', array('user' => $hosting->username));
        if (! isset($account['error']) || $account['error'] == '0') {
            $account_data = array();
            $disk_usage = 0;
            $bandwidth_usage = 0;
            foreach ($account as $domain => $data) {
                $domain = str_replace('_', '.', $domain);
                $split_data = explode(":", $data);

                $account_data[] = array(
                    'domain' => $domain,
                    'bandwidth_usage' => $split_data[0],
                    'bandwidth_limit' => $split_data[1],
                    'disk_usage' => $split_data[2],
                    'log_usage' => $split_data[3],
                    'subdomains' => $split_data[4],
                    'suspended' => $split_data[5],
                    'quota' => $split_data[6],
                    'ssl' => $split_data[7],
                    'cgi' => $split_data[8],
                    'php' => $split_data[9]
                );

                $disk_usage = $disk_usage + $split_data[2];
                $bandwidth_usage = $bandwidth_usage + $split_data[0];
            }

            $hosting->diskspace_usage = $disk_usage;
            $hosting->bandwidth_usage = $bandwidth_usage;
            $hosting->last_sync = time();
            $hosting->save();

            return $account_data;
        } else {
            return false;
        }
    }

    public function serverDetails()
    {
        $this->serverConnection();
        $data = $this->cmd->get('/CMD_API_IP_CONFIG');
        $server_details = array();

        if (! empty($data) && isset($data['ip'])) {

            $server_details['hostname'] = $this->server->hostname . ' (' . $data['ip'] . ')';

        }

        return $server_details;
    }

    public function generateUsername($domain)
    {
        $domain = preg_replace("/[^A-Za-z0-9 ]/", '', $domain);

        return substr($domain, 0, 8);
    }
}
