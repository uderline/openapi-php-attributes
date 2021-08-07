# Before you start ...

## What is OPAG ?
OpenApi PHP Attributes Generator is a library made to automatically generate a valid OA3 file which describes your API.
Each path is described in your PHP files (often your controllers) using [PHP 8 attributes](https://stitcher.io/blog/attributes-in-php-8).

## Why should I use OpenApi in the first place ?
Having a file describing your API will allow you to share it with your community (if it's public) and it can be used 
with softwares such as Swagger or Postman.


## Recommendations
We recommend having some knowledge on OpenApi: https://spec.openapis.org/oas/latest.html


## Which version compatible
Only the version 3 is compatible with this library


# How to read this documentation
- The documentation is split into 3 sections: the header, paths and components
- Only required values are mentioned in the documentation according to the OpenApi [documentation](https://spec.openapis.org/oas/latest.html)
- Optional fields can be accessed by respecting the parameters order or using the syntax `#[Element(prop1: "value1")]`

# Let's start describing !

## Header
On any class, start by adding the required [Info](https://spec.openapis.org/oas/latest.html#info-object) object.
```php
#[Info("Title of your project", "Version of the API")]
// Example:
#[Info("OpenApi PHP Generator", "1.0.0")]
```

Then, you can add one or more [Server](https://spec.openapis.org/oas/latest.html#server-object) object(s).
```php
#[Server("https://api.url.com", "API description")]
```

## Security
You can add the [Security Scheme](https://spec.openapis.org/oas/v3.1.0#security-scheme-object) policy of your API.
```php
#[SecurityScheme("securityKey", "type", "name", "in", "scheme", "description", "bearerFormat")]
```


## Paths
Paths must be described on methods (1 path = 1 route) in a class (a controller).

```php
<?php
#[Controller]
class Controller {

}
```

### Declare routes (GET example)
```php
<?php
#[Controller]
class Controller {
    #[
        Route(Route::GET, "/path")
    ]
    public function getAll() { }
}
```

### Declare a parameter (PUT example)
```php
<?php
#[Controller]
class Controller {
    #[
        Route(Route::PUT, "/path/{id}")
    ]
    public function put(#[Parameter] int $id) { }
}
```

### Declare a request body with properties
```php
<?php
#[Controller]
class Controller {
    #[
        Route(Route::PUT, "/path"),
        Property(PropertyType::STRING, "prop1"),
        Property(PropertyType::INT, "prop2"),
    ]
    public function post() { }
}
```
Available property types:
- `PropertyType::STRING`: "string"
- `PropertyType::INT`: "integer"
- `PropertyType::BOOLEAN`: "boolean"
- `PropertyType::REF`: "ref" (explained below)

### Declare a simple response (POST example)
```php
<?php
#[Controller]
class Controller {
    #[
        Route(Route::POST, "/path"),
        Property(PropertyType::STRING, "prop1"),
        Property(PropertyType::INT, "prop2"),
        Response(201)
    ]
    public function post() { }
}
```
*The response code is actually optional*

### Declare a response with properties (GET example)
This method will return:
```json
{
    "prop1": "value1",
    "prop2": ["val1", "val2", "val3"],
    "prop3": "value3"
}
```

```php
<?php
#[Controller]
class Controller {
    #[
        Route(Route::GET, "/path/{id}"),
        Response,
        Property(PropertyType::STRING, "prop1"),
        Property(PropertyType::ARRAY, "prop2"),
        PropertyItems(PropertyType::STRING),
        Property(PropertyType::INT, "prop3")
    ]
    public function get(#[Parameter] int $id) { }
}
```

### Create OA3 components ($ref)
A component is a PHP class and will often be an entity (or a model). 
On this entity, declare a schema and it's properties. The name of the schema is the name of the class.

```php
<?php
#[
    Schema,
    Property(PropertyType::STRING, "prop1"),
    Property(PropertyType::STRING, "prop2", enum: ["val1", "val2", "val3"]), // Enum type
    Property(PropertyType::ARRAY, "prop3"),
    PropertyItems(PropertyType::INT),
    Property(PropertyType::BOOLEAN, "prop4"),
]
class Entity {

}
```

### Use a component

#### Response
```php
<?php
class Controller {
    #[Response(ref: Entity::class)]
    public function get() {}
}
```
Means the method will return something like:
```json
{
    "prop1": "value 1",
    "prop2": "val2",
    "prop3": [1, 2, 3],
    "prop4": true
}
```

If instead we have
```php
<?php
class Controller {
    #[Response(schemaType: ResponseType::ARRAY, ref: Entity::class)]
    public function get() {}
}
```
then it means the method will return something like:
```json
[
    {
        "prop1": "value 1",
        "prop2": "val2",
        "prop3": [1, 2, 3],
        "prop4": true
    },
    {
        "prop1": "value 12",
        "prop2": "val22",
        "prop3": [12, 22, 32],
        "prop4": false
    }
]
```
