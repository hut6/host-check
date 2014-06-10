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
        $defaults = array(
            'timeout' => 20,
            'logging' => false,
        );

        $this->options = array_merge($defaults, $options);
    }


    public function all($urls)
    {
        $queue = new RequestsQueue();
        $queue->getDefaultOptions()
            ->set(CURLOPT_TIMEOUT, $this->options['timeout'])
            ->set(CURLOPT_RETURNTRANSFER, true)
            ->set(CURLOPT_SSL_VERIFYPEER, false)
//            ->set(CURLOPT_FOLLOWLOCATION, true)
        ;

        $originalUrls = $urls;

        $queue->addListener(
            'complete',
            function (Event $event) use (&$urls, $originalUrls, $queue) {
                /** @var Response $response */
                $response = $event->response;
                $info = $response->getInfo();

                $result = array('http_code' => $info['http_code']);

                if ($info['http_code'] === 200) {
                    echo sprintf("\nOK : %s", $info['url']);
                } elseif ($info['http_code'] === 302 || $info['http_code'] === 301) {
                    if($info['url'] != $info['redirect_url'] && !in_array($info['redirect_url'], $originalUrls)) {
                        $queue->attach(new Request($info['redirect_url']));
                    } else {
                        echo sprintf("\n%s: %s", $info['http_code'], $info['url']);
                    }
                } elseif ($info['http_code'] === 0) {
                    if ($response->hasError()) {
                        $result['message'] = $response->getError()->getMessage();
                        echo sprintf("\n---: %s (%s)", $info['url'], $result['message']);
                    } else {
                        echo sprintf("\n???: %s", $info['url']);
                        $result['message'] = 'Timeout';
                    }
                } else {
                    echo sprintf("\n%s: %s", $info['http_code'], $info['url']);
                }

                $this->responses[$info['url']] = $result;

                if(count($urls)) {
                    $queue->attach(new Request(array_pop($urls)));
                }
            }
        );

        $concurrency = 200;

        for ($i = 0; $i < $concurrency; $i++) {
            $queue->attach(new Request(array_pop($urls)));
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

    public function percent($responses, $code)
    {
        $count = 0;
        $urls = array_keys($responses);
        array_unique($urls);
        $total = count($responses);

        foreach($responses as $response) {
            if($response['http_code'] === $code) {
                $count++;
            }
        }

        return sprintf('%s%%', number_format($count / $total * 100, 2));
    }
}




