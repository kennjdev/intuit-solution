<?php

namespace IntuitSolutions\BigCommerce;

use IntuitSolutions\BigCommerce\Helpers;
use GuzzleHttp\Client;

/**
 * Interact with the BigCommerce API.
 */
class BigCommerceAPI
{
    public static $defaultOptions = array(
        'version' => 2,
        'json' => true
    );

    /**
     * Create a BigCommerce API object.
     *
     * @param string|null $store Optional store hash. If not set or null, uses the current store
     * @param string|null $token Optional access token. If not set or null, uses the current store's access token
     */
    public function __construct($store = null, $token = null, $client_id = null, Client $guzzleClient = null)
    {
        $this->hash = $store;
        $this->token = $token;
        $this->client_id = $client_id;

        $this->endpoint = sprintf('https://api.bigcommerce.com/stores/%s', $this->hash);
        $this->guzzleClient = $guzzleClient ?: new Client();
    }


    /**
     * Set the access token for the API.
     *
     * @param string $token BC access token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    public function setStore($store)
    {
        $this->hash = $store;
    }

    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }

    /**
     * Get the store being used for the API.
     *
     * @return string Store hash
     */
    public function getStore()
    {
        return $this->hash;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Set the access token being used for the API.
     *
     * @return string BC access token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * GET a resource.
     *
     * @param string $resource API resource
     * @param string $filter   Request filters
     * @param int    $version  API version
     * @param string[] $responseHeaders Will be populated with the response headers
     *
     * @return object API result
     */
    public function get($resource, $filter = 'limit=250', $options = array())
    {
        return $this->curl('GET', $resource, $filter, null, $options);
    }

    /**
     * POST a resource.
     *
     * @param string $resource API resource
     * @param object $object   The resource object to POST
     * @param int    $version  API version
     * @param bool   $json True if the resource object should be sent as JSON, otherwise it will be sent as formdata
     * @param string[] $responseHeaders Will be populated with the response headers
     *
     * @return object API result
     */
    public function post($resource, $object, $options = array())
    {
        return $this->curl('POST', $resource, null, $object, $options);
    }

    /**
     * PUT a resource.
     *
     * @param string $resource API resource
     * @param object $object   The resource object to PUT
     * @param int    $version  API version
     * @param bool   $json True if the resource object should be sent as JSON, otherwise it will be sent as formdata
     * @param string[] $responseHeaders Will be populated with the response headers
     *
     * @return object API result
     */
    public function put($resource, $object, $options = array())
    {
        return $this->curl('PUT', $resource, null, $object, $options);
    }

    /**
     * DELETE a resource.
     *
     * @param string $resource API resource
     * @param int    $version  API version
     * @param string[] $responseHeaders Will be populated with the response headers
     *
     * @return object API result
     */
    public function delete($resource, $options = array())
    {
        return $this->curl('DELETE', $resource, null, null, $options);
    }

    /**
     * Run an API request using cURL.
     *
     * @param string $method   Request method
     * @param string $resource BigCommerce API resource
     * @param string $body     Request body
     * @param int    $version  API version
     * @param string[] $responseHeaders Will be populated with the response headers
     *
     * @return object API result
     */
    protected function curl($method, $resource, $filter = null, $body = null, $options = array())
    {
        $options = array_merge(static::$defaultOptions, $options);

        if (is_array($filter)) {
            $filter = http_build_query($filter);
        }

        if ($options['version'] == 2 && !preg_match('/^hooks/', $resource)) {
            $resource .= '.json';
        }

        if ($filter) {
            $resource .= '?' . $filter;
        }

        $headers = [
            'X-Auth-Client' => $this->client_id,
            'X-Auth-Token' => $this->token,
            'Accept' => 'application/json'
        ];

        $url = sprintf('%s/v%d/%s', $this->endpoint, $options['version'], $resource);

        $this->responseHeaders = [];

        $guzzleOpts = ['http_errors' => false, 'headers' => $headers];

        if ($body) {
            $has_file = params_has_file($body);

            if ($options['json'] && $has_file) {
                throw new \TypeError('File cannot be sent in JSON mode');
            } elseif ($options['json']) {
                $guzzleOpts['json'] = $body;
            } elseif ($has_file) {
                $guzzleOpts['multipart'] = params_to_multipart($body);
            } else {
                $guzzleOpts['form_params'] = $body;
            }
        }

        $response = $this->guzzleClient->request($method, $url, $guzzleOpts);

        $code = $response->getStatusCode();

        $responseHeaders = $response->getHeaders();
        $result = (string)$response->getBody();

        $object = json_decode($result);

        return $object;
    }
}
