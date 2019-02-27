# Changelog

## 1.4.0 (27.02.2019)
+ Implement `metrics` method on `LKDev\HetznerCloud\Models\Server\Server` (@paulus7, https://github.com/LKDevelopment/hetzner-cloud-php-sdk/pull/10)

## 1.3.1 (20.02.2019)
+ Fix a error on the update methods.

## 1.3.0 (05.11.2018)
 + Add Volumes support (`LKDev\HetznerCloud\Models\Volumes\Volumes` & `LKDev\HetznerCloud\Models\Volumes\Volume`)
 + Add all API Response headers to every `LKDev\HetznerCloud\APIResponse`
 
##### Deprecation
 + Deprecate and ignore the `$backup_window` parameter on `LKDev\HetznerCloud\Models\Server\Server::enableBackups`
 
## 1.2.0 (06.09.2018)
 + Add `httpClient`-Method to `LKDev\HetznerCloud\HetznerAPIClient`
 + Add `labels`-Property to `LKDev\HetznerCloud\Models\FloatingIp\FloatingIP`
 + Add `labels`-Property to `LKDev\HetznerCloud\Models\Server\Server`
 + Add `labels`-Property to `LKDev\HetznerCloud\Models\Image\Image`
 + Add `labels`-Property to `LKDev\HetznerCloud\Models\SSHKey\SSHKey`
 + Add `update`-Method to `\LKDev\HetznerCloud\Models\FloatingIp\FloatingIp`-Model for easily updateing the server meta data
   ```php
   $floatingIP->update(['description' => 'my-updated-floating-description','labels' => ['Key' => 'value]);
   ```
 + Add `update`-Method to `\LKDev\HetznerCloud\Models\SSHKey\SSHKey`-Model for easily updateing the server meta data
   ```php
   $floatingIP->update(['name' => 'my-updated-sshkey-name','labels' => ['Key' => 'value]);
   ```   
 + You can now use the `labels`-Key on every `update`-Method, for easily updating the Labels
 + Add `LKDev\HetznerCloud\RequestOpts` - Class for easily customize the request opts. Could be used for filtering with the label selector.
 + Add the parameter `$requestOpts` to all `all`-Methods
--- 
## 1.1.0 (14.08.2018)
 + Add `update`-Method to `\LKDev\HetznerCloud\Models\Servers\Server`-Model for easily updateing the server meta data
   ```php
   $server->update(['name' => 'my-updated-server-name']);
   ````
 + Soft Deprecating the `changeName`-Method on `\LKDev\HetznerCloud\Models\Servers\Server`-Model, please use the `update`-Method now. As of Version 1.5.0 this method will trigger a `Deprecated`
 + Rename `ApiResponse` to `APIResponse 
---
## 1.0.0 (09.08.2018)
##### Breaking Changes
* _Servers Class_
  The `create` method was split into two different methods `createInLocation` for creating a server in a location and `createInDatacenter`for creating a server in a datacenter

* _All "action" like `Server::powerOn()` methods_ now return an instance of `LKDev\HetznerCloud\ApiResponse` instead an instance of `LKDev\HetznerCloud\Models\Actions\Action`. If you want to the the underlying action object just use the value _action_ as parameter of the `getResponsePart` on the `LKDev\HetznerCloud\ApiResponse` object. Of course you could use _server_ or _wss_url_ as parameter like in the official Hetzner Cloud documentation

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
