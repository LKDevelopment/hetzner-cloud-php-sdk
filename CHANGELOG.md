# Changelog


## unreleased
 + Add `update`-Method to `\LKDev\HetznerCloud\Models\Servers\Server`-Model for easily updateing the server meta data
   ```php
   $server->update(['name' => 'my-updated-server-name']);
   ````
 + Soft Deprecating the `changeName`-Method on `\LKDev\HetznerCloud\Models\Servers\Server`-Model, please use the `update`-Method now. As of Version 1.5.0 this method will trigger a `Deprecated`
---
## 1.0.0 (09.08.2018)
##### Breaking Changes
* _Servers Class_
  The `create` method was split into two different methods `createInLocation` for creating a server in a location and `createInDatacenter`for creating a server in a datacenter

* _All "action" like `Server::powerOn()` methods_ now return an instance of `LKDev\HetznerCloud\ApiResponse` instead an instance of `LKDev\HetznerCloud\Models\Actions\Action`. If you want to the the underlying action object just use the value _action_ as parameter of the `getResponsePart` on the `LKDev\HetznerCloud\ApiResponse`object. Of course you could use _server_ or _wss_url_ as parameter like in the official Hetzner Cloud documentation

* _All resource object constructors_ now require an instance of the internal GuzzleHttpClient `LKDev\HetznerCloud\Clients\GuzzleClient` if you use the shortcut methods this is done for you. 

##### Non Breaking Changes
###### Shortcut methods
The `HetznerApiClient`- object has now a method for every resource, for easily accessing the underlying object.
Instead of
```php
// < v1.0.0
$apiKey = '{InsertApiTokenHere}';

$hetznerClient = new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);
$serverEndpoint = new  \LKDev\HetznerCloud\Models\Servers\Servers();
$serverEndpoint->all();
```
you could now do simply
```php
// >= v1.0.0
$apiKey = '{InsertApiTokenHere}';

$hetznerClient = new \LKDev\HetznerCloud\HetznerAPIClient($apiKey);
$hetznerClient->servers()->all();