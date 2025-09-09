<?php

declare(strict_types=1);

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validator;
use RxAnte\OAuth\Handlers\Common\JwtConfigurationFactory;
use RxAnte\OAuth\Handlers\RxAnte\Internal\EmptyJwt;
use RxAnte\OAuth\Handlers\RxAnte\Internal\JwtFactory;

describe('JwtFactory', function (): void {
    uses()->group('JwtFactory');

    it(
        'returns empty jwt when token is empty',
        function (): void {
            $sut = new JwtFactory(jwtConfigurationFactory: Mockery::mock(
                JwtConfigurationFactory::class,
            ));

            $result = $sut->createFromToken('');

            expect($result)->toBeInstanceOf(EmptyJwt::class);
        },
    );

    it(
        'returns empty jwt when exception is thrown parsing jwt',
        function (): void {
            $conf = Mockery::mock(Configuration::class);
            $conf->expects('parser')->andThrow(
                new Exception(),
            );

            $confFactory = Mockery::mock(JwtConfigurationFactory::class);
            $confFactory->expects('create')->andReturn($conf);

            $sut = new JwtFactory(jwtConfigurationFactory: $confFactory);

            $result = $sut->createFromToken('mock-token');

            expect($result)->toBeInstanceOf(EmptyJwt::class);
        },
    );

    it('returns jwt from jwt parser', function (): void {
        $constraint1 = Mockery::mock(Constraint::class);

        $constraint2 = Mockery::mock(Constraint::class);

        $jwtToken = Mockery::mock(UnencryptedToken::class);

        $validator = Mockery::mock(Validator::class);
        $validator->expects('assert')
            ->with($jwtToken, $constraint1, $constraint2);

        $parser = Mockery::mock(Parser::class);
        $parser->expects('parse')->with('mock-token')
            ->andReturn($jwtToken);

        $conf = Mockery::mock(Configuration::class);
        $conf->expects('parser')->andReturn($parser);
        $conf->expects('validationConstraints')->andReturn([
            $constraint1,
            $constraint2,
        ]);
        $conf->expects('validator')->andReturn($validator);

        $confFactory = Mockery::mock(JwtConfigurationFactory::class);
        $confFactory->expects('create')->andReturn($conf);

        $sut = new JwtFactory(jwtConfigurationFactory: $confFactory);

        $result = $sut->createFromToken('mock-token');

        expect($result)->toBe($jwtToken);
    });
});
