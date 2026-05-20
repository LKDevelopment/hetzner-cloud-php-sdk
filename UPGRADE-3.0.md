# Upgrade Guide: v2.x to v3.0

This guide provides instructions for upgrading your application from `hetzner-cloud-php-sdk` version 2.x to 3.0.

## Breaking Changes

### PHP Version Requirement
- **Requirement:** PHP 8.2 or higher is now required.
- **Action:** Ensure your server or environment is running PHP 8.2+.

### Removal of `illuminate/collections`
The dependency on `illuminate/collections` has been removed to reduce the library's footprint and avoid dependency conflicts. All internal usages of `collect()` have been replaced with native PHP array functions.

- **Impact:** If your application relied on the SDK returning Laravel Collections, this will no longer happen.
- **Action:** Use native PHP array functions like `array_map()`, `array_filter()`, or wrap the returned arrays in a collection yourself if you still need them.

**Before (v2.x):**
```php
// Some internal methods or if you were using collect() on SDK results
$names = collect($hetznerClient->servers()->all())->map(fn($s) => $s->name);
```

**After (v3.0):**
```php
// Results are now always native arrays
$servers = $hetznerClient->servers()->all();
$names = array_map(fn($s) => $s->name, $servers);

// If you want to keep using Collections in your project:
// composer require illuminate/collections
$names = collect($hetznerClient->servers()->all())->map(fn($s) => $s->name);
```

### `delete()` Method Return Type Change
The `delete()` method on all resource models now returns an `APIResponse` object (or `null`) instead of a `boolean`. This provides more information about the API response, such as the `Action` created by the deletion.

- **Impact:** Any code checking for `if ($model->delete())` will still work as `APIResponse` is truthy, but it is better to update your logic if you were relying on the boolean result.

**Before (v2.x):**
```php
$success = $server->delete(); // returned bool
if ($success) {
    // ...
}
```

**After (v3.0):**
```php
$response = $server->delete(); // returns ?APIResponse
if ($response !== null) {
    $action = $response->action; // You can now access the action
    // ...
}
```

### Native Type Hints
Many method signatures have been updated to include native PHP 8.1 type hints. This improves IDE support and static analysis.

- **Impact:** If you have extended SDK classes and overridden methods, you may need to update your method signatures to match the new type hints to avoid PHP fatal errors.

---

## New Features
Version 3.0 also introduces several new features and improvements:
- Support for **Primary IPs**.
- Support for **Placement Groups**.
- Support for **Firewalls** (including label selectors).
- Improved support for **Load Balancers**.
- Improved support for **Managed Certificates**.

## Summary for LLMs
- Minimum PHP: `8.2`
- Dependency removed: `illuminate/collections`
- `delete()` returns: `?LKDev\HetznerCloud\APIResponse` (was `bool`)
- All list methods return: `array`
- Models use native type hints for parameters and return types.
