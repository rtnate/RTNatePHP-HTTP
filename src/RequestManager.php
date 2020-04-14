<?php

namespace RTNatePHP\HTTP;

use GuzzleHttp\Client as Client;
use Psr\Http\Message\ResponseInterface;

/**
 * GuzzleHttp\Client wrapper for making HTTP Requests
 * 
 * @author Nate Taylor - nate@rtelectronix.com
 * @version 1.0 
 * 
 * @use Psr\Http\Message\StreamInterface
 */
class RequestManager
{
    /**
     * The Request URL
     * @var string
     */
    protected $url = '';

    /**
     * The array of HTTP headers to send with the request
     * @var array
     */
    protected $headers = []; 

    /**
     * The HTTP Method (i.e. 'GET', 'POST', 'UPDATE')
     * @var string
     */
    protected $reqMethod = 'GET';

    /**
     * The Guzzle Client instance that will perform the request
     * @var Client
     */
    protected $client = null;

    /**
     * The HTTP query parameters
     * @var array
     */
    protected $query = [];

    /**
     * The HTTP Response
     * 
     * @var ResponseInterface
     */
    protected $response = null;

    /**
     * Construct a new ExternalRequest object
     * 
     * @param string $url - The request url
     * @param Client|null $client - The Client object to use.  If not supplied
     *                              one will be created automatically
     */
    public function __construct($url = '', $client = null)
    {
        if ($url) $this->url = $url;
        if ($client == null)
        {
            $this->client = new Client();
        }
        else if (!($client instanceof Client))
        {
            throw new \TypeError("Supplied $client must be an instance of ".Client::class);
        }
        else
        {
            $this->client = $client;
        }
    }

    /**
     * Sets the request URL
     * 
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * Set any HTTP request headers using an associated array.  
     * 
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Sets query parameters as a $key => $value pair.
     * 
     * @param string $key   The parameter key 
     * @param mixed $value  The parameter value, must be a valid strval
     */
    public function setQueryParam(string $key, $value)
    {
        $this->query[$key] = $value;
    }

    /**
     * Sets query parameters using the supplied associative array.
     * 
     * @param array $params The query parameters to set
     */
    public function setQueryParams(array $params)
    {
        $this->query = array_merge($this->query, $params);
    }

    /**
     * Sets the HTTP request method (i.e. 'GET', 'POST', 'UPDATE')
     * 
     * @param string $newMethod
     */
    public function setMethod(string $newMethod)
    {
        $this->reqMethod = $newMethod;
    }

    /**
     * Gets the query parameters as a urlencoded string
     * 
     * @return string - The query string
     */
    protected function getQueryString()
    {
        $query = "";
        if (count($this->query)) $query = "?";
        foreach($this->query as $key => $value)
        {
            $query .= "{$key}=";
            $query .=   urlencode($value);
            $query .=  "&";
        }
        $query = rtrim($query, "&");
        return $query;
    }

    /**
     * Retrieve the HTTP Response returned after performing make()
     * 
     * @return ResponseInterface|null Returns null when no response has been received
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Retrieve the HTTP Response Body as a stream returned after performing make()
     * 
     * @return StreamInterface|null Returns null when no response has been received
     */
    public function getResponseBody()
    {
        if ($this->response) return $this->getResponse()->getBody();
        else return null;
    }

    /**
     * Retrieve the HTTP Response Body Contents returned after performing make()
     * 
     * @return string|null Returns null when no response has been received
     */
    public function getResponseContents()
    {
        if ($this->response) return $this->getResponseBody()->getContents();
        else return '';
    }

    /**
     * Perform the HTTP request with the supplied body
     * 
     * @param string $body - The HTTP Request body
     * @throws HTTPRequestException on failure
     * @return true If the HTTP Request succeeds
     */
    public function make(string $body = '')
    {
        $this->response = null;
        try{
            $query = $this->getQueryString();
            $reqUri = $this->url . $query;
            $response = $this->client->request($this->reqMethod, $reqUri, ['body' => $body, 'headers' => $this->headers]);
            $this->response = $response;
            return true;
        }
        catch(\Throwable $e)
        {
            throw new HTTPRequestException($e->getMessage(), $e->getCode(), $e);
        }
        return false;
    }

}