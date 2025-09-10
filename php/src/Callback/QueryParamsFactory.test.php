<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Callback\QueryParamsFactory;

describe('QueryParamsFactory', function (): void {
    uses()->group('QueryParamsFactory');

    it(
        'returns QueryParams from',
        function (array $queryParams): void {
            $request = Mockery::mock(ServerRequestInterface::class);
            $request->expects('getQueryParams')
                ->andReturn($queryParams);

            $sut = new QueryParamsFactory();

            $result = $sut->createFromRequest($request);

            expect($result->code)->toBe(
                $queryParams['code'] ?? '',
            );

            expect($result->state)->toBe(
                $queryParams['state'] ?? '',
            );
        },
    )->with([
        'empty query params array' => ['queryParams' => []],
        'from query params array with only "code"' => [
            'queryParams' => ['code' => 'mock-code'],
        ],
        'from query params array with only "state"' => [
            'queryParams' => ['state' => 'mock-code'],
        ],
        'from query params array with all attributes' => [
            'queryParams' => [
                'state' => 'mock-code',
                'code' => 'mock-code',
            ],
        ],
    ]);
});
