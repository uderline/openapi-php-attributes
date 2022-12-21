---
title: Root attributes
layout: home
nav_order: 3
---

# Root attributes

On any class, start by adding the required [Info](https://spec.openapis.org/oas/latest.html#info-object) object.

```php
<?php
#[Info("Title of your project", "Version of the API")]
// Example:
#[Info("OpenApi PHP Generator", "1.0.0")]
```

Then, you can add one or more [Server](https://spec.openapis.org/oas/latest.html#server-object) object(s).

```php
<?php
#[Server("https://api.url.com", "API description")]
```

## Security schemes

You can add the [Security Scheme](https://spec.openapis.org/oas/v3.1.0#security-scheme-object) policy of your API.

Be aware that depending on the selected type of security, you will have to add (or not) attributes

```php
<?php
#[SecurityScheme("type", "name", "in", "scheme", "description", "bearerFormat")]
```

## Security

Now that we have defined security schemes, we can declare the security policy of our API.

```php
<?php
#[Security(["type", "or", "name_as_snake_case")]
```

If I have provided the optional parameter *name* in the security scheme, I will have to use it in the security attribute.
```php
<?php
// With a provided name
#[SecurityScheme(type: "http", name: "http security", scheme: "Bearer", bearerFormat: "JWT format")]
#[Security(["http_security"])]
class MyController {

}

// Without a provided name
#[SecurityScheme(type: "http", scheme: "Bearer", bearerFormat: "JWT format")]
#[Security(["http"])]
class MyController {

}
```
