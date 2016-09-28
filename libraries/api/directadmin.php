<?php

namespace Addon\Directadmin\Libraries\Api;

class Directadmin
{
    public $host;
    public $port;
    public $user;
    public $pass;

    public $cmd;
    public $response;
    public $request;

    public $last_response_header;
    public $last_response_content;

    public function __construct($host, $port, $user, $pass, $ssl = false)
    {
        $this->cmd = new \Whsuite\Http\Http;
        $this->request = $this->cmd->newRequest();

        if ($ssl) {
            $this->host = 'https://' . $host;
        } else {
            $this->host = 'http://' . $host;
        }

        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;


    }

    public function get($command, $params = array())
    {
        $this->request->setAuth(
            $this->user,
            $this->pass
        );

        if (! empty($params)) {
            $params = http_build_query($params);
            $this->request->setUrl($this->host.':'.$this->port.$command.'?'.$params);
        } else {
            $this->request->setUrl($this->host.':'.$this->port.$command);
        }

        try {
            $Response = $this->cmd->send($this->request);
        } catch (\Guzzle\Http\Exception\BadResponseException $e) {

            return false;
        } catch (\Exception $e) {

            return false;
        }

        if ($Response->isSuccessful()) {
            $this->last_response_header = $Response->getHeaders();
            $this->last_response_content = $this->formatResponse($Response->getBody());

            return $this->last_response_content;
        }
        
        return false;
    }

    public function post($command, $params = array())
    {
        $this->request->setAuth(
            $this->user,
            $this->pass
        );

        if (! empty($params)) {
            $this->request->setContent(http_build_query($params));
        }

        $this->request->setUrl($this->host.':'.$this->port.$command);

        $this->request->setMethod('post');

        try {
            $Response = $this->cmd->send($this->request);
        } catch (\Exception $e) {
            die(\App::get('translation')->get('server_connection_failed'));
        }

        if ($Response->isSuccessful()) {
            $this->last_response_header = $Response->getHeaders();
            $this->last_response_content = $this->formatResponse($Response->getBody());

            return $this->last_response_content;
        }

        return false;
    }


    protected function formatResponse($response)
    {
        parse_str(urldecode($response), $array);
        return $array;
    }
}
