Wrapper around GuzzleHttp\Client for making synchronous HTTP Requests for external resources
# Requirements
Uses [Guzzle](http://docs.guzzlephp.org/en/stable/) v6 or later 
# Usage
Basic usage is as follows:
```
$url = "https://api.example.com/bananas"
$request = new \RTNatePHP\HTTP\RequestManager($url);
$request->setHeaders(['accept' => 'application/json']);
try
{
    $request->make();
    $data = $request->getResponseContents();
    $bananas = json_decode($data, true);
} 
catch(HttpRequestException $e)
{
    $httpErrorCode = $e->getCode();
    \\Handle failed request here 
}
```
The Request Manager throws an \RTNatePHP\HttpRequestException on failure.  

### Alternate Constructor Options
You can also set the url later
```
$request = new RequestManager();
$request->setUrl('https://api.example.com/');
```
Or pass in an existing GuzzleHttp/Client rather than having the 
class create one for you
```
$client - new GuzzleHttp\Client();
$request = new RequestManager($url, $client);
```
### Changing The Request Method
By default the manager will perform a GET request.
The request method can be changed like this:
```
$request->setMethod('POST')
```
### Adding Headers
Headers can be added as an associative array
```
$request->setHeaders(['accepts' => 'application/json', 'Content-Type' => 'application/json'])
```

### Setting Query Parameters
Instead of adding query parameters to the url, they can be set like follows.
To get https://api.example.com/car?color=red&type=sedan:
```
$request->setQueryParam('cars', 'red');
$request->setQueryParam('type', 'sedan');
//OR
$request->setQueryParams(['cars' => 'red', 'type' => 'sedan']);
```
Query parameters have to be strings, or convertible by strval() 

### Setting the Request Body
A non-empty request body can be passed as a string to the make() method.
```
$data = ['User' => 'Frank', 'Location' => 'Outer Space'];
$body = json_encode($data);
$request->setMethod('POST');
$request->make($body);
```

### Retrieving the Response
Response contents can be easily retrieved as a string:
```
$data = $request->getResponseContents();
```
This will return an empty string if a valid response hasn't been received yet.
The ResponseInterface can be accessed directly:
```
$response = $request->getResponse();
```
Or the StreamInterface to the body:
```
$body = $request->getResponseBody();
```
Both of these methods will return null if no valid response has been received

### Inheriting this class
Inheriting this class can be useful.  
It's easy to override the default url
```
protected $url = 'https://api.example.com/v1/'; 
```
Or any default headers
```
protected $headers = ['accepts' => 'application/json']; 
```
Or the method
```
protected $reqMethod = 'POST'; 
```
Or any default query parameters
```
protected $query = [];
```