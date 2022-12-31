---
title: Quick example
layout: home
nav_order: 1
---

# Quick example

This is a quick example of a controller that uses attributes to generate an OpenAPI document.

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
                                    "$ref": "#/components/schemas/YourObject"
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
            "YourObject": {
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
