<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingTraversableTypeHintSpecification
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RxAnte\OAuth\Request\RequestResponse;

describe('RequestResponse', function (): void {
    uses()->group('RequestResponse');

    readonly class RequestResponseMockStack
    {
        public RequestResponse $sut;

        public function __construct(
            string $bodyContent = '',
            int $statusCode = 200,
        ) {
            $headers = [
                'header1' => ['header1Response'],
                'header2' => ['header2Response'],
            ];

            $response = Mockery::mock(ResponseInterface::class);

            $response->allows('getHeaders')->andReturn($headers);

            $response->allows('hasHeader')->andReturnUsing(
                function (string $name) use ($headers): bool {
                    return isset($headers[$name]);
                },
            );

            $response->allows('getHeader')->andReturnUsing(
                function (string $name) use ($headers): array {
                    return $headers[$name] ?? [];
                },
            );

            $response->allows('getHeaderLine')->andReturnUsing(
                function (string $name) use ($headers): string {
                    return $headers[$name][0] ?? '';
                },
            );

            $body = Mockery::mock(StreamInterface::class);
            $body->allows('__toString')->andReturn($bodyContent);
            $response->allows('getBody')->andReturn($body);

            $response->allows('getStatusCode')->andReturn(
                $statusCode,
            );

            $response->allows('getReasonPhrase')->andReturn(
                'foo-reason-phrase',
            );

            $this->sut = new RequestResponse($response);
        }
    }

    test(
        'getHeaders() returns the headers',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->getHeaders())->toBe([
                'header1' => ['header1Response'],
                'header2' => ['header2Response'],
            ]);
        },
    );

    test(
        'hasHeader() assesses headers correctly',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->hasHeader('header1'))
                ->toBeTrue();

            expect($mockStack->sut->hasHeader('header2'))
                ->toBeTrue();

            expect($mockStack->sut->hasHeader('header3'))
                ->toBeFalse();

            expect($mockStack->sut->hasHeader('foo'))
                ->toBeFalse();
        },
    );

    test(
        'getHeader() returns correct header(s)',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->getHeader('header1'))
                ->toBe(['header1Response']);

            expect($mockStack->sut->getHeader('header2'))
                ->toBe(['header2Response']);

            expect($mockStack->sut->getHeader('header3'))
                ->toBe([]);

            expect($mockStack->sut->getHeader('foo'))
                ->toBe([]);
        },
    );

    test(
        'getHeaderLine() returns correct header(s)',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->getHeaderLine('header1'))
                ->toBe('header1Response');

            expect($mockStack->sut->getHeaderLine('header2'))
                ->toBe('header2Response');

            expect($mockStack->sut->getHeaderLine('header3'))
                ->toBe('');

            expect($mockStack->sut->getHeaderLine('foo'))
                ->toBe('');
        },
    );

    test(
        'getBody() returns body',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->getBody())->toBe('');

            $mockStack = new RequestResponseMockStack(bodyContent: 'foo-body');

            expect($mockStack->sut->getBody())->toBe(
                'foo-body',
            );
        },
    );

    test(
        'getStatusCode() returns status code',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->getStatusCode())->toBe(
                200,
            );

            $mockStack = new RequestResponseMockStack(statusCode: 345);

            expect($mockStack->sut->getStatusCode())->toBe(
                345,
            );
        },
    );

    test(
        'getReasonPhrase() returns reason phrase',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->getReasonPhrase())->toBe(
                'foo-reason-phrase',
            );
        },
    );

    test(
        'isOk() returns correct status',
        function (): void {
            $mockStack = new RequestResponseMockStack();

            expect($mockStack->sut->isOk())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 345);

            expect($mockStack->sut->isOk())->toBeFalse();
        },
    );

    test(
        'is1xx() returns correct status',
        function (): void {
            $mockStack = new RequestResponseMockStack(statusCode: 99);

            expect($mockStack->sut->is1xx())->toBeFalse();

            $mockStack = new RequestResponseMockStack(statusCode: 100);

            expect($mockStack->sut->is1xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 199);

            expect($mockStack->sut->is1xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 200);

            expect($mockStack->sut->is1xx())->toBeFalse();
        },
    );

    test(
        'is2xx() returns correct status',
        function (): void {
            $mockStack = new RequestResponseMockStack(statusCode: 199);

            expect($mockStack->sut->is2xx())->toBeFalse();

            $mockStack = new RequestResponseMockStack(statusCode: 200);

            expect($mockStack->sut->is2xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 299);

            expect($mockStack->sut->is2xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 300);

            expect($mockStack->sut->is2xx())->toBeFalse();
        },
    );

    test(
        'is3xx() returns correct status',
        function (): void {
            $mockStack = new RequestResponseMockStack(statusCode: 299);

            expect($mockStack->sut->is3xx())->toBeFalse();

            $mockStack = new RequestResponseMockStack(statusCode: 300);

            expect($mockStack->sut->is3xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 399);

            expect($mockStack->sut->is3xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 400);

            expect($mockStack->sut->is3xx())->toBeFalse();
        },
    );

    test(
        'is4xx() returns correct status',
        function (): void {
            $mockStack = new RequestResponseMockStack(statusCode: 399);

            expect($mockStack->sut->is4xx())->toBeFalse();

            $mockStack = new RequestResponseMockStack(statusCode: 400);

            expect($mockStack->sut->is4xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 499);

            expect($mockStack->sut->is4xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 500);

            expect($mockStack->sut->is4xx())->toBeFalse();
        },
    );

    test(
        'is5xx() returns correct status',
        function (): void {
            $mockStack = new RequestResponseMockStack(statusCode: 499);

            expect($mockStack->sut->is5xx())->toBeFalse();

            $mockStack = new RequestResponseMockStack(statusCode: 500);

            expect($mockStack->sut->is5xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 599);

            expect($mockStack->sut->is5xx())->toBeTrue();

            $mockStack = new RequestResponseMockStack(statusCode: 600);

            expect($mockStack->sut->is5xx())->toBeFalse();
        },
    );

    test(
        'getJson() returns error on invalid',
        function (): void {
            $mockStack = new RequestResponseMockStack(bodyContent: 'noop');

            expect($mockStack->sut->getJson())->toBe([
                'error' => 'invalid_json',
                'error_description' => 'The response body is not valid JSON',
                'message' => 'The response body is not valid JSON',
            ]);
        },
    );

    test(
        'getJson() returns decoded json',
        function (): void {
            $mockStack = new RequestResponseMockStack(
                bodyContent: '{"foo":"bar","asdf":"fdsa"}',
            );

            expect($mockStack->sut->getJson())->toBe([
                'foo' => 'bar',
                'asdf' => 'fdsa',
            ]);
        },
    );
});
