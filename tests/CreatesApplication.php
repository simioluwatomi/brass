<?php

namespace Tests;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Client\Response;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function createHttpClientResponse(string $filepath, int $status = 200, array $headers = []): Response
    {
        return new Response(new GuzzleResponse(
            $status,
            $headers,
            file_get_contents(base_path($filepath))
        ));
    }
}
