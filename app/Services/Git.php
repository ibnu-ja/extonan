<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Process;

final readonly class Git
{
    public function getLatestTag(): string
    {
        return $this->envOrFallback('APP_VERSION', 'describe --tags --abbrev=0');
    }

    public function getAppBranch(): string
    {
        return $this->envOrFallback('APP_BRANCH', 'rev-parse --abbrev-ref HEAD');
    }

    public function getAppCommitHash(): string
    {
        return $this->envOrFallback('APP_COMMIT_HASH', 'rev-parse --short HEAD');
    }

    public function getRepoUrl(): ?string
    {
        return $this->envOrFallback('APP_REPO_URL', 'remote get-url origin');
    }

    private function envOrFallback(string $envKey, string $gitCommand): string
    {
        $env = env($envKey);
        if ($env !== null && $env !== '') {
            return $env;
        }

        return trim(
            Process::path(base_path())->run(['git', ...explode(' ', $gitCommand)])->output(),
        );
    }
}
