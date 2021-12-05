# Quick example
This is the easiest example we could've made.

```php
<?php
#[Info("OpenApi PHP Generator", "1.0.0")]
#[Controller]
class Controller {
    #[
        GET("/path/{id}"),
        Response(200, ref: YourObject::class)
    ]
    public function get(#[IDParam] $id) { }

    #[
        POST("/path"),
        Property(Type::STRING, "prop1"),
        Property(Type::INT, "prop2"),
        Response(200, ref: YourObject::class)
    ]
    public function post() { }
}

#[
    Schema,
    Property(Type::STRING, "prop1"),
    Property(Type::INT, "prop2"),
]
class YourObject {

}
```

This will return:
```json
{
    "openapi": "3.0.0",
    "info": {
        "title": "OpenApi PHP Generator",
        "version": "1.0.0"
    },
    "paths": {
        "/path/{id}": {
            "get": {
                "parameters": [
                    {
                        "name": "id",
                        "in": "path",
                        "schema": {
                            "type": "integer",
                            "minimum": 1
                        },
                        "required": true
                    }
                ],
                "responses": {
                    "200": {
                        "description": "",
                        "content": {
                            "application/json": {
                                "schema": {
                                    "$ref": "#/components/schemas/DummyComponent"
                                }
                            }
                        }
                    }
                }
            }
        },
        "/path": {
            "post": {
                "requestBody": {
                    "content": {
                        "application/json": {
                            "schema": {
                                "type": "object",
                                "properties": {
                                    "prop1": {
                                        "type": "string"
                                    },
                                    "prop2": {
                                        "type": "integer"
                                    }
                                }
                            }
                        }
                    }
                },
                "responses": {
                    "200": {
                        "description": "",
                        "content": {
                            "application/json": {
                                "schema": {
                                    "$ref": "#/components/schemas/YourObject"
                                }
                            }
                        }
                    }
                }
            }
        }
    },
    "components": {
        "schemas": {
            "Event": {
                "type": "object",
                "properties": {
                    "prop1": {
                        "type": "string"
                    },
                    "prop2": {
                        "type": "integer"
                    }
                }
            }
        }
    }
}
```

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
#### Easy example
```php
<?php
#[Controller]
class Controller {
    #[
        GET("/path")
    ]
    public function getAll() { }
}
```

#### Comprehensive example
```php
<?php
#[Controller]
class Controller {
    #[
        GET(route: "/path", tags: ["Entity"], description: "Get all entities")
        // OR
        Route(Route::GET, route: "/path", tags: ["Entity"], description: "Get all entities")
    ]
    public function getAll() { }
}
```

### Declare a parameter (PUT example)
#### Easy example
```php
<?php
#[Controller]
class Controller {
    #[
        PUT("/path/{id}")
    ]
    public function put(#[Parameter] int $id) { }
}
```

#### Comprehensive example
```php
<?php
#[Controller]
class Controller {
    #[
        PUT("/path/{id}")
    ]
    public function put(
    #[Parameter(description: "Id of an entity", in: "path", required: true, example: 123, format: "uuid")] int $id
    // OR
    #[IDParam] int $id
    ) { }
}
```
> Please note that we do not specify the type because it's using the type of the variable

`IDParam` will set a _minimum_ property to 1

### Declare a request body with properties
#### Easy example
```php
<?php
#[Controller]
class Controller {
    #[
        PUT("/path"),
        Property(Type::STRING, "prop1"),
        Property(Type::INT, "prop2"),
    ]
    public function post() { }
}
```
Available property types:
- `Type::STRING`: "string"
- `Type::INT`: "integer"
- `Type::BOOLEAN`: "boolean"
- `Type::ID`: "id" (shortcut for an integer strictly greater than 0)
- `Type::REF`: "ref" (explained below)

#### Comprehensive example
```php
<?php
#[Controller]
class Controller {
    #[
        PUT("/path"),
        Property(type: Type::STRING, property: "prop1", description: "Property 1", example: "abcd", format: "hostname", enum: ["host1", "host2"]),
        Property(Type::INT, "prop2"),
    ]
    public function post() { }
}
```

### Declare a simple response (POST example)
#### Easy example
```php
<?php
#[Controller]
class Controller {
    #[
        POST("/path"),
        Property(Type::STRING, "prop1"),
        Property(Type::INT, "prop2"),
        Response(201)
    ]
    public function post() { }
}
```
*The response code is actually optional*

#### Comprehensive example
```php
<?php
#[Controller]
class Controller {
    #[
        POST("/path"),
        Property(Type::STRING, "prop1"),
        Property(Type::INT, "prop2"),
        Response(code: 201, description: "Created", responseType: Type::JSON, schemaType: Type::OBJECT, ref: Component::class)
    ]
    public function post() { }
}
```

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
        GET("/path/{id}"),
        Response,
        Property(Type::STRING, "prop1"),
        Property(Type::ARRAY, "prop2"),
        PropertyItems(Type::STRING),
        Property(Type::INT, "prop3")
    ]
    public function get(#[IDParam] int $id) { }
}
```

### Create OA3 components ($ref)
A component is a PHP class and will often be an entity (or a model). 
On this entity, declare a schema and it's properties. The name of the schema is the name of the class.

```php
<?php
#[
    Schema,
    Property(Type::STRING, "prop1"),
    Property(Type::STRING, "prop2", enum: ["val1", "val2", "val3"]), // Enum type
    Property(Type::ARRAY, "prop3"),
    PropertyItems(Type::INT),
    Property(Type::BOOLEAN, "prop4"),
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
    #[Response(schemaType: Type::ARRAY, ref: Entity::class)]
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
