<?php

namespace Tests\Unit;

use App\Services\GoogleSearchConsoleService;
use ReflectionMethod;
use Tests\TestCase;

class GoogleSearchConsoleServiceTest extends TestCase
{
    public function test_it_builds_the_official_webmasters_v3_endpoint(): void
    {
        $method = new ReflectionMethod(GoogleSearchConsoleService::class, 'searchAnalyticsUrl');
        $url = $method->invoke(new GoogleSearchConsoleService, 'https://pusattalilanyard.com/');

        $this->assertSame(
            'https://www.googleapis.com/webmasters/v3/sites/https%3A%2F%2Fpusattalilanyard.com%2F/searchAnalytics/query',
            $url,
        );
    }
}
