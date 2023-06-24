<?php

declare(strict_types=1);

namespace Unit;

use OpenApiGenerator\Attributes\Info;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    #[Test]
    public function json_must_return_the_apis_info(): void
    {
        $schema = new Info("title", "1.0.0", "description");

        $this->assertEqualsCanonicalizing([
            'title' => 'title',
            'version' => '1.0.0',
            'description' => 'description',
        ], $schema->jsonSerialize());
    }
}