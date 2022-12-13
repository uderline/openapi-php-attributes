# OpenAPI PHP Attributes Generator

This CLI Tool is able to generate an OpenApi JSON file description according to PHP attributes contained in your files.

## ⚠️ Missing something ?
Just open an issue saying what's missing ! Feel free to open a PR but we recommend opening an issue beforehand.

## Where to start ?
1 - Install the package openapi-php-attributes-generator with composer.

```bash
composer require uderline/openapi-php-attributes
```

2 - Describe your API by following this documentation: https://uderline.github.io/openapi-php-attributes/

3 - Generate the JSON file:
```bash
php ./vendor/bin/opag /src/files/project openapi.json
```

A new file called `openapi.json` has been generated !

## Example
```php
#[Controller]
class Controller {
    #[
        GET("/path/{id}", ["Tag1", "Tag2"], "Description of the method"),
        Property(PropertyType::STRING, "prop1", description: "Property description", enum: ["val1", "val2"]),
        Property(PropertyType::INT, "prop2", example: 1),
        Property(PropertyType::BOOLEAN, "prop3"),
        Property(PropertyType::REF, "prop4", ref: RefSchema::class)
        Response(ref: SchemaName::class, description: "Response description")
    ]
    public function get(#[Parameter("Parameter description")] int $id): JsonResponse {
        // ...
    }
    
    #[
        DynamicBuilder(MyFrameworkResolver::class),
        Property(PropertyType::REF, "prop4", ref: RefSchema::class)
        Response(ref: SchemaName::class, description: "Response description")
    ]
    public function getSomethingElse(#[Parameter("Parameter description")] int $id): JsonResponse {
        // ...
    }
}

#[
    Schema,
    Property(Type::STRING, "Property 1"),
    Property(Type::INT, "Property 2"),
]
class RefSchema
{
    public string $property1;
    public int $property2;
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
                                    },
                                    "prop4": {
                                        "$ref": "#/components/schemas/RefSchema"
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

```
class MyFrameworkResolver
{
    public function build(PathMethodBuilder $pathBuilder, $instance, $parameters, $reflectionClass, $method) 
    {
        // Here you can add your own logic to build the path
        // How to add dependencies? Laravel uses global container so it can be done like this:
        $route = Routes::getRoutes()->findByControllerAndMethodName()->getUri();

        $pathBuilder->setRoute($route);
    }
}
```