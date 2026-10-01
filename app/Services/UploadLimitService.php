<?php

namespace App\Services;

/**
 * Reports the upload limits PHP is actually enforcing (php.ini) so forms never
 * promise more than the web server will accept. A file that is larger than
 * upload_max_filesize is discarded by PHP before it reaches Laravel, which is
 * why oversized uploads surface as "the file failed to upload".
 */
class UploadLimitService
{
    public function maxFileBytes(): int
    {
        return min(
            $this->iniBytes('upload_max_filesize'),
            $this->iniBytes('post_max_size'),
        );
    }

    public function maxFileKilobytes(): int
    {
        return max(1, (int) floor($this->maxFileBytes() / 1024));
    }

    public function maxFileCount(): int
    {
        $count = (int) ini_get('max_file_uploads');

        return $count > 0 ? $count : 1;
    }

    public function maxFileLabel(): string
    {
        $bytes = $this->maxFileBytes();

        return $bytes >= PHP_INT_MAX ? 'unlimited' : $this->formatBytes($bytes);
    }

    public function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1048576) {
            return round($bytes / 1024).' KB';
        }

        return rtrim(rtrim(number_format($bytes / 1048576, 1, '.', ''), '0'), '.').' MB';
    }

    public function iniBytes(string $directive): int
    {
        return $this->toBytes((string) ini_get($directive));
    }

    /**
     * Convert a php.ini shorthand size (for example "2M" or "512K") to bytes.
     */
    public function toBytes(string $shorthand): int
    {
        $shorthand = trim($shorthand);

        if ($shorthand === '' || $shorthand === '-1') {
            return PHP_INT_MAX;
        }

        $value = (int) $shorthand;

        if ($value === 0) {
            return PHP_INT_MAX;
        }

        return match (strtoupper(substr($shorthand, -1))) {
            'K' => $value * 1024,
            'M' => $value * 1048576,
            'G' => $value * 1073741824,
            default => $value,
        };
    }
}
