# OpenAPI PHP Generator

CLI Tool able to generate an Open API JSON file according to PHP attributes.

### Generate
Generate JSON file: `php ./vendor/uderline/openapi-php-attributes/bin.php /path/to/your/src/files/project`.

### Describe your API
#### Controller
```php
#[\OpenApiGenerator\Attributes\Controller]
class Controller {
    // ...
}
```

#### Route
```php
use OpenApiGenerator\Attributes\Route;

#[\OpenApiGenerator\Attributes\Controller]
class Controller {
    #[Route(Route::GET, "/path", ["Tag1", "Tag2"], "Description of the method")]
    public function get(): void {
        // ...
    }
}
```

#### Get Parameter
```php
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Route;

#[OpenApiGenerator\Attributes\Controller]
class Controller {
    #[Route(Route::GET, "/path/{id}", ["Tag1", "Tag2"], "Description of the method")]
    public function get(#[Parameter("Parameter description")] int $id): void {
        // ...
    }
}
```

#### Body properties
```php
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Types\PropertyType;

#[OpenApiGenerator\Attributes\Controller]
class Controller {
    #[
        Route(Route::GET, "/path/{id}", ["Tag1", "Tag2"], "Description of the method"),
        Property(PropertyType::STRING, "prop1", description: "Property description", enum: ["val1", "val2"]),
        Property(PropertyType::INT, "prop2", example: 1),
        Property(PropertyType::BOOLEAN, "prop3"),
    ]
    public function get(#[Parameter("Parameter description")] int $id): void {
        // ...
    }
}
```

#### Response properties
```php
use OpenApiGenerator\Attributes\Parameter;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\Types\PropertyType;
use Symfony\Component\HttpFoundation\JsonResponse;

#[OpenApiGenerator\Attributes\Controller]
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

The `ref` is a way of rendering:
```json
{
    "responses": {
        "200": {
            "description": "Response description",
            "content": {
                "application/json": {
                    "schema": {
                        "items": {
                            "$ref": "#/components/schemas/SchemaName"
                        }
                    }
                }
            }
        }
    }
}
```

#### Create a schema ($ref)
```php
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Types\ItemsType;
use OpenApiGenerator\Types\PropertyType;
use OpenApiGenerator\Types\SchemaType;

#[
    Schema(SchemaType::OBJECT),
    Property(PropertyType::STRING, "Prop1"),
    Property(PropertyType::INT, "Prop2"),
    Property(PropertyType::ARRAY, "Prop3"),
    PropertyItems(ItemsType::INT),
    Property(PropertyType::ARRAY, property: "prizes", description: "Prizes"),
    PropertyItems(ItemsType::REF, OtherModel::class),
]
class Model {

}
```