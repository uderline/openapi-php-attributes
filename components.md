---
title: Components
layout: home
nav_order: 5
---

# Create OA3 components ($ref)

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
        Response
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

