<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Process;

final readonly class Git
{
    private const array PROVIDERS = [
        'github' => '/github\.com/i',
        'gitlab' => '/gitlab\.(com|org)/i',
        // TODO: implement codeberg provider
        // 'codeberg' => '/codeberg\.org/i',
        // TODO: implement bitbucket provider
        // 'bitbucket' => '/bitbucket\.org/i',
        // TODO: implement gitea provider
        // 'gitea' => '/gitea\.(com|io)/i',
    ];

    private const array HTTPS_DOMAINS = [
        'github' => 'github.com',
        'gitlab' => 'gitlab.com',
        'codeberg' => 'codeberg.org',
        'bitbucket' => 'bitbucket.org',
    ];

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

    public function getRepoProvider(): string
    {
        $url = $this->getRepoUrl();

        if ($url === null || $url === '') {
            return 'unknown';
        }

        foreach (self::PROVIDERS as $name => $pattern) {
            if (preg_match($pattern, $url) === 1) {
                return $name;
            }
        }

        return 'unknown';
    }

    public function getRepoHttpsUrl(): ?string
    {
        $url = $this->getRepoUrl();

        if ($url === null || $url === '') {
            return null;
        }

        $provider = $this->getRepoProvider();
        $domain = self::HTTPS_DOMAINS[$provider] ?? null;

        if ($domain === null) {
            return null;
        }

        // Handle SSH format: git@github.com:user/repo.git
        if (preg_match('/git@([^:]+):(.+)\.git$/', $url, $matches) === 1) {
            return "https://{$domain}/{$matches[2]}";
        }

        // Handle HTTPS format: https://github.com/user/repo.git
        if (preg_match('#https?://[^/]+/(.+)\.git$#', $url, $matches) === 1) {
            return "https://{$domain}/{$matches[1]}";
        }

        // Handle git:// protocol format: git://github.com/user/repo.git
        if (preg_match('#git://[^/]+/(.+)\.git$#', $url, $matches) === 1) {
            return "https://{$domain}/{$matches[1]}";
        }

        return null;
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
