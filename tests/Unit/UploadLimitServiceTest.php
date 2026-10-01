<?php

use App\Services\UploadLimitService;

it('converts php.ini shorthand sizes to bytes', function () {
    $service = app(UploadLimitService::class);

    expect($service->toBytes('2M'))->toBe(2097152)
        ->and($service->toBytes('20m'))->toBe(20971520)
        ->and($service->toBytes('512K'))->toBe(524288)
        ->and($service->toBytes('1G'))->toBe(1073741824)
        ->and($service->toBytes('8388608'))->toBe(8388608);
});

it('treats empty, zero, and -1 ini values as unlimited', function () {
    $service = app(UploadLimitService::class);

    expect($service->toBytes(''))->toBe(PHP_INT_MAX)
        ->and($service->toBytes('   '))->toBe(PHP_INT_MAX)
        ->and($service->toBytes('0'))->toBe(PHP_INT_MAX)
        ->and($service->toBytes('-1'))->toBe(PHP_INT_MAX);
});

it('formats byte sizes for display', function () {
    $service = app(UploadLimitService::class);

    expect($service->formatBytes(512))->toBe('512 B')
        ->and($service->formatBytes(524288))->toBe('512 KB')
        ->and($service->formatBytes(4194304))->toBe('4 MB')
        ->and($service->formatBytes(1572864))->toBe('1.5 MB');
});

it('takes the smallest of upload_max_filesize and post_max_size from php.ini', function () {
    $service = app(UploadLimitService::class);

    $expected = min($service->iniBytes('upload_max_filesize'), $service->iniBytes('post_max_size'));

    expect($service->maxFileBytes())->toBe($expected)
        ->and($service->maxFileKilobytes())->toBe((int) floor($expected / 1024))
        ->and($service->maxFileCount())->toBeGreaterThanOrEqual(1);
});

it('labels the effective server limit for error messages', function () {
    $service = app(UploadLimitService::class);

    $bytes = $service->maxFileBytes();
    $expected = $bytes >= PHP_INT_MAX ? 'unlimited' : $service->formatBytes($bytes);

    expect($service->maxFileLabel())->toBe($expected);
});
