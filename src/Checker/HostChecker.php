<?php
/**
 * User: Ryan Castle <ryan@dwd.com.au>
 * Date: 4/06/14
 */

namespace Checker;

use cURL\Event;
use cURL\Request;
use cURL\RequestsQueue;
use cURL\Response;

class HostChecker
{
    protected $responses = array();
    protected $options;

    function __construct($options = array())
    {
        $this->options = $options;
    }


    public function all($urls)
    {
        $queue = new RequestsQueue();
        $queue->getDefaultOptions()
            ->set(CURLOPT_TIMEOUT, 20)
            ->set(CURLOPT_RETURNTRANSFER, true);

        $queue->addListener(
            'complete',
            function (Event $event) {
                /** @var Response $response */
                $response = $event->response;
                $info = $response->getInfo();

                $result = array('http_code' => $info['http_code']);

                if ($info['http_code'] === 200) {
                    echo '✓';
                } elseif ($info['http_code'] === 302 || $info['http_code'] === 301) {
                    echo '➔';
                } elseif ($info['http_code'] === 0) {
                    if ($response->hasError()) {
                        $result['message'] = $response->getError()->getMessage();
                    } else {
                        $result['message'] = 'Timeout';
                    }
                    echo '✘';
                } else {
                    echo '?';
                }
                $this->responses[$info['url']] = $result;
            }
        );

        foreach ($urls as $url) {
            $request = new Request($url);
            $queue->attach($request);
        }

        ob_start();
        while ($queue->socketPerform()) {
            echo '.';
            $queue->socketSelect();
            if(!empty($this->options['logging'])) {
                ob_flush();
            }
        }
        $this->log = ob_get_clean();

        return $this->responses;
    }
}




