<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RxAnte\OAuth\Callback\GetCallbackAction;
use RxAnte\OAuth\Callback\GetCallbackResponder;
use RxAnte\OAuth\Callback\GetCallbackResponderFactory;
use RxAnte\OAuth\Callback\QueryParams;
use RxAnte\OAuth\Callback\QueryParamsFactory;
use RxAnte\OAuth\Routes\RequestMethod;

describe('GetCallbackAction', function (): void {
    uses()->group('GetCallbackAction');

    it(
        'creates route',
        function (string|null $pattern): void {
            $isString = is_string($pattern);

            $patternExpectation = $isString ? $pattern : '/auth/callback';

            $route = match ($isString) {
                true => GetCallbackAction::createRoute($pattern),
                false => GetCallbackAction::createRoute(),
            };

            expect($route->requestMethod->name)->toBe(
                RequestMethod::GET->name,
            );

            expect($route->pattern)->toBe($patternExpectation);

            expect($route->class)->toBe(
                GetCallbackAction::class,
            );
        },
    )->with([
        'with custom pattern' => ['pattern' => '/custom/pattern'],
        'without default pattern' => ['pattern' => null],
    ]);

    it(
        'creates response from responder',
        function (): void {
            $params = new QueryParams(
                code: 'mock-code',
                state: 'mock-state',
            );

            $request = Mockery::mock(ServerRequestInterface::class);

            $inputResponse = Mockery::mock(ResponseInterface::class);

            $outputResponse = Mockery::mock(ResponseInterface::class);

            $responder = Mockery::mock(GetCallbackResponder::class);

            $responder->expects('respond')
                ->with($request, $inputResponse)
                ->andReturn($outputResponse);

            $responderFactory = Mockery::mock(
                GetCallbackResponderFactory::class,
            );
            $responderFactory->expects('create')
                ->with($params, $request)
                ->andReturn($responder);

            $queryParamsFactory = Mockery::mock(
                QueryParamsFactory::class,
            );
            $queryParamsFactory->expects('createFromRequest')
                ->with($request)
                ->andReturn($params);

            $sut = new GetCallbackAction(
                queryParamsFactory: $queryParamsFactory,
                responderFactory: $responderFactory,
            );

            $result = $sut->__invoke($request, $inputResponse);

            expect($result)->toBe($outputResponse);
        },
    );
});
