---
title: Paths
layout: home
nav_order: 4
---

# Paths

Paths must be described on methods (1 path = 1 route) in a class (e.g. a controller).

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
        PUT("/path/{id}/{parameter}"),
        PathParameter(name: "parameter", type: Type::STRING, description: "An other parameter", in: "path", required: true, example: "abcd")
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

#### IDParam with a model

If you are using auto-wiring and injecting objects as arguments, you can still set an `IDParam` but make sure you set a
property as object id.

```php
// Model
#[
  Schema,
  Property(Type::STRING, "id", isObjectId: true)
]
class ObjectModel {
}

// Controller
class Controller {
    public function put(#[IDParam] ObjectModel $model) {
        // ...
    }
}
```

This will generate:
```json
    ...
    "parameters": [
        {
            "name": "id",
            "in": "path",
            "schema": {
                "type": "string"
            },
            "required": true
        }
    ],
    ...
```

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
        // You may also use the enum syntax below (see BackedEnum)
        Property(type: Type::STRING, property: "prop2", description: "Property 2", example: "abcd", format: "hostname", enum: BackedEnum::class),
        Property(Type::INT, "prop2"),
    ]
    public function post() { }
}

// BackedEnum.php
enum BackedEnum: string {
    case HOST1 = "host1";
    case HOST2 = "host2";
}
```

#### Using a custom request

```php
<?php

// CustomRequest.php
#[
    Schema,
    Property(type: Type::STRING, property: "prop1", description: "Property 1", example: "abcd", format: "hostname", enum: ["host1", "host2"]),
    Property(Type::INT, "prop2"),
]
class CustomRequest extends \Symfony\Component\HttpFoundation\Request {
    
}

// Controller.php

class Controller {
    #[
        POST("/path"),
        Response(201)
    ]
    public function post(CustomRequest $request): Response
    {
        //
    }
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
    "prop2": [
        "val1",
        "val2",
        "val3"
    ],
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

#### Request

```php
<?php
class Controller {
    #[
        POST("/path"),
        Property(Type::INT, "non_ref_prop"),
        Property(Type::REF, "entity_props", ref: Entity::class),
        Response()
    ]
    public function get() {}
}
```

Means the method will want something like:

```json
{
    "non_ref_prop": 1,
    "entity_props": {
        "prop1": "value 1",
        "prop2": "val2",
        "prop3": [
            1,
            2,
            3
        ],
        "prop4": true
    }
}
```

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
    "prop3": [
        1,
        2,
        3
    ],
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
        "prop3": [
            1,
            2,
            3
        ],
        "prop4": true
    },
    {
        "prop1": "value 12",
        "prop2": "val22",
        "prop3": [
            12,
            22,
            32
        ],
        "prop4": false
    }
]
```

