<?php

declare(strict_types=1);

use Psr\Log\LogLevel;
use RxAnte\OAuth\NoOpLogger;

describe('NoOpLogger', function (): void {
    uses()->group('NoOpLogger');

    it('logs to nowhere', function (): void {
        $logger = new NoOpLogger();

        $logger->emergency('emergency');
        $logger->alert('alert');
        $logger->critical('critical');
        $logger->error('error');
        $logger->warning('warning');
        $logger->notice('notice');
        $logger->info('info');
        $logger->debug('debug');
        $logger->log(LogLevel::INFO, 'log');

        // If no exception is thrown, the test passes
        expect(true)->toBeTrue();
    });
});
