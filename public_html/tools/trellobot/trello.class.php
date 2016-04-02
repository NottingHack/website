<?php

/*
    This is heavily based on https://github.com/coderstephen/slack-client
    It uses the same dependencies and reactPHP loop

    Slack, however, is an RPC-style API, and Trello is REST, so changes to the
    apiCall method were necessary

    Only limited api endpoints are supported, as required
 */

use GuzzleHttp\Guzzle;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;

class Trello
{
    const BASE_URL = 'https://api.trello.com/1';

    private $httpClient;

    private $loop;

    private $appKey;

    private $token;

    private $boardId;


    public function __construct(LoopInterface $loop, GuzzleHttp\ClientInterface $httpClient = null)
    {
        $this->loop = $loop;
        $this->httpClient = $httpClient ?: new GuzzleHttp\Client();
    }

    public function setCredentials($appKey, $token)
    {
        $this->appKey = $appKey;
        $this->token = $token;
    }

    public function setBoard($boardId)
    {
        $this->boardId = $boardId;
    }

    public function getAllUsers()
    {
        $endpoint = '/boards/' . $this->boardId . '/members';
        return $this->apiCall($endpoint, 'GET');
    }

    public function getAllCards()
    {
        $endpoint = '/boards/' . $this->boardId . '/cards';
        return $this->apiCall($endpoint, 'GET');
    }

    public function getAllLists()
    {
        $endpoint = '/boards/' . $this->boardId . '/lists';
        return $this->apiCall($endpoint, 'GET');
    }

    private function apiCall($endpoint, $method)
    {
        // create the request url
        $requestUrl = self::BASE_URL . $endpoint;

        // add security
        $requestUrl .= '?key=' . $this->appKey . '&token=' . $this->token;

        // send a post request with all arguments
        switch ($method) {
            case 'GET':
                $promise = $this->httpClient->getAsync($requestUrl);
                break;
            case 'DELETE':
                $promise = $this->httpClient->deleteAsync($requestUrl);
                break;
            case 'POST':
                $promise = $this->httpClient->postAsync($requestUrl);
                break;
            case 'PUT':
                $promise = $this->httpClient->putAsync($requestUrl);
                break;
        }

        // Add requests to the event loop to be handled at a later date.
        $this->loop->futureTick(function () use ($promise) {
            $promise->wait();
        });

        // When the response has arrived, parse it and resolve. Note that our
        // promises aren't pretty; Guzzle promises are not compatible with React
        // promises, so the only Guzzle promises ever used die in here and it is
        // React from here on out.
        $deferred = new Deferred();
        $promise->then(function (ResponseInterface $response) use ($deferred) {
            // get the response as a json object
            $payload = $this->decodeJSON($response->getBody());

            // check if there was an error
            if ($response->getStatusCode() == 200) {
                $deferred->resolve($payload);
            } else {
                // make a nice-looking error message and throw an exception
                $niceMessage = ucfirst(str_replace('_', ' ', $payload['error']));
                $deferred->reject(new ApiException($niceMessage));
            }
        });

        return $deferred->promise();
    }

    function decodeJSON($json) {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new \UnexpectedValueException('Invalid JSON message.');
        }

        return $data;
    }

}