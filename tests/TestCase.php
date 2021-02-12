<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** @var String
     *
     * A string that's prefixed to the URIs that are
     * accessed in the tests. (i.e. /api/[test_URI]).
     */
    protected $apiBasePrefix;

    public function setUp(): void
    {
        parent::setUp(); // Required to include the TestCase set up code as well.

        // Prefixes the URIs with the API version number (i.e. /v1.0/[test_URI])
        $this->apiBasePrefix = '/'. config('app.api_version');
    }
}
