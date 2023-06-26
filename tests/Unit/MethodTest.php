<?php

declare(strict_types=1);

namespace OpenApiGenerator\Tests\Unit;

use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\PropertyItems;
use OpenApiGenerator\Attributes\RequestBody;
use OpenApiGenerator\Attributes\Response;
use OpenApiGenerator\Attributes\Route;
use OpenApiGenerator\DefinitionCheckerException;
use OpenApiGenerator\Method;
use OpenApiGenerator\Type;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MethodTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function add_property_is_added_to_response(): void
    {
        $method = (new Method())
            ->setRoute(new Route('GET', '/path'))
            ->addResponse(new Response())
            ->addProperty(new Property(Type::STRING, 'prop'))
            ->setRequestBody(new RequestBody());

        $expected = [
            'responses' => [
                '200' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'prop' => [
                                        'type' => 'string',
                                        'description' => ''
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $this->assertEquals($expected, json_decode(json_encode($method->jsonSerialize()), true));
    }

    #[Test]
    public function add_property_is_added_to_request(): void
    {
        $method = (new Method())
            ->setRoute(new Route('GET', '/path'))
            ->setRequestBody(new RequestBody())
            ->addProperty(new Property(Type::STRING, 'prop'))
            ->addResponse(new Response());

        $expected = [
            "requestBody" => [
                "content" => [
                    "application/json" => [
                        "schema" => [
                            "type" => "object",
                            "properties" => [
                                "prop" => [
                                    "type" => "string",
                                    "description" => ""
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => '',
                ]
            ],
        ];

        $this->assertEquals($expected, json_decode(json_encode($method->jsonSerialize()), true));
    }

    #[Test]
    public function add_property_throws_an_exception_when_method_has_neither_request_or_response(): void
    {
        $method = (new Method())
            ->setRoute(new Route('GET', '/path'));

        $this->expectException(DefinitionCheckerException::class);
        $method->addProperty(new Property(Type::STRING, 'prop'));
    }

    #[Test]
    public function add_property_items_sets_request_content_to_return_an_array(): void
    {
        $method = (new Method())
            ->setRoute(new Route('GET', '/path'))
            ->setRequestBody(new RequestBody())
            ->addPropertyItems(new PropertyItems('string'))
            ->addResponse(new Response());

        $expected = [
            "requestBody" => [
                "content" => [
                    "application/json" => [
                        "schema" => [
                            "type" => "array",
                            "items" => [
                                "type" => "string",
                            ],
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => '',
                ]
            ],
        ];

        $this->assertEquals($expected, json_decode(json_encode($method->jsonSerialize()), true));
    }

    #[Test]
    public function add_property_items_sets_response_content_to_return_an_array(): void
    {
        $method = (new Method())
            ->setRoute(new Route('GET', '/path'))
            ->setRequestBody(new RequestBody())
            ->addResponse(new Response())
            ->addPropertyItems(new PropertyItems('string'));

        $expected = [
            'responses' => [
                '200' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'string',
                                ],
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $this->assertEquals($expected, json_decode(json_encode($method->jsonSerialize()), true));
    }

    #[Test]
    public function add_property_items_updates_last_request_property(): void
    {
        $method = (new Method())
            ->setRoute(new Route('GET', '/path'))
            ->setRequestBody(new RequestBody())
            ->addProperty(new Property(Type::ARRAY, 'prop'))
            ->addPropertyItems(new PropertyItems('string'))
            ->addResponse(new Response());

        $expected = [
            "requestBody" => [
                "content" => [
                    "application/json" => [
                        "schema" => [
                            "type" => "object",
                            "properties" => [
                                "prop" => [
                                    "type" => "array",
                                    "items" => [
                                        "type" => "string",
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'responses' => [
                '200' => [
                    'description' => '',
                ]
            ],
        ];

        $this->assertEquals($expected, json_decode(json_encode($method->jsonSerialize()), true));
    }

    #[Test]
    public function add_property_items_updates_last_response_property(): void
    {
        $method = (new Method())
            ->setRoute(new Route('GET', '/path'))
            ->setRequestBody(new RequestBody())
            ->addResponse(new Response())
            ->addProperty(new Property(Type::ARRAY, 'prop'))
            ->addPropertyItems(new PropertyItems('string'));

        $expected = [
            'responses' => [
                '200' => [
                    'description' => '',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'prop' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'string',
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $this->assertEquals($expected, json_decode(json_encode($method->jsonSerialize()), true));
    }
}
