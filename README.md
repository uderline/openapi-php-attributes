# OpenAPI PHP Attributes Generator

This CLI Tool is able to generate an OpenApi JSON file description according to PHP attributes contained in your files.


## ⚠️ Missing something ?
Just open an issue saying what's missing ! Feel free to open a PR but we recommend opening an issue beforehand. 


## Where to start ?
- `composer require uderline/openapi-php-attributes`
- Describe your API by following this documentation: https://uderline.github.io/openapi-php-attributes/
- Then, generate the JSON file: `php ./vendor/uderline/openapi-php-attributes/opag /src/files/project /save/the/file`.

A new file called `openapi.json` has been generated !


## Example
```php
#[Controller]
class Controller {
    #[
        Route(Route::GET, "/path/{id}", ["Tag1", "Tag2"], "Description of the method"),
        Property(PropertyType::STRING, "prop1", description: "Property description", enum: ["val1", "val2"]),
        Property(PropertyType::INT, "prop2", example: 1),
        Property(PropertyType::BOOLEAN, "prop3"),
        Response(200, "Response description", ref: SchemaName::class)
    ]
    public function get(#[Parameter("Parameter description")] int $id): JsonResponse {
        // ...
    }
}
```

Will generate
```json
{
    "paths": {
        "/path/{id}": {
            "post": {
                "tags": [
                    "Tag1",
                    "Tag2"
                ],
                "summary": "Description of the method",
                "parameters": [
                    {
                        "name": "id",
                        "in": "path",
                        "description": "Parameter description",
                        "required": true,
                        "schema": {
                            "type": "integer"
                        }
                    }
                ],
                "requestBody": {
                    "content": {
                        "application/json": {
                            "schema": {
                                "type": "object",
                                "properties": {
                                    "prop1": {
                                        "type": "string",
                                        "description": "Property description",
                                        "enum": [
                                            "val1",
                                            "val2"
                                        ]
                                    },
                                    "prop2": {
                                        "type": "integer",
                                        "description": ""
                                    },
                                    "prop3": {
                                        "type": "boolean",
                                        "description": ""
                                    }
                                }
                            }
                        }
                    }
                },
                "responses": {
                    "200": {
                        "description": "Response description",
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
        }
    }
}
```
