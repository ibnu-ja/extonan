<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;

/**
 * todo: barangkali create interface/facade atau abstract class, implementation is github
 */
final readonly class Git
{
    private const string CACHE_KEY_PREFIX = 'git_';

    private const int CACHE_TTL = 86400 * 7;

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
        return $this->remember('latest_tag', fn (): string => $this->fromConfigOrGit('describe --tags --abbrev=0', 'git.app_version'));
    }

    public function getAppBranch(): string
    {
        return $this->remember('branch', fn (): string => $this->fromConfigOrGit('rev-parse --abbrev-ref HEAD', 'git.app_branch'));
    }

    public function getAppCommitHash(): string
    {
        return $this->remember('commit_hash', fn (): string => $this->fromConfigOrGit('rev-parse --short HEAD', 'git.app_commit_hash'));
    }

    public function getRepoUrl(): ?string
    {
        return $this->remember('repo_url', fn (): ?string => $this->fromConfigOrGit('remote get-url origin', 'git.app_repo_url'));
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

    public function flush(): void
    {
        Cache::tags('git')->flush();
    }

    private function remember(string $key, \Closure $callback): mixed
    {
        return Cache::tags('git')->remember(
            self::CACHE_KEY_PREFIX.$key,
            self::CACHE_TTL,
            $callback,
        );
    }

    private function fromConfigOrGit(string $command, string $configKey): string
    {
        try {
            $value = $this->gitCommand($command);

            if ($value !== null && $value !== '') {
                return $value;
            }
        } catch (\Throwable) {
            // git not available
        }

        return config($configKey) ?? '';
    }

    private function gitCommand(string $command): ?string
    {
        $result = trim(
            Process::path(base_path())->run(['git', ...explode(' ', $command)])->output(),
        );

        return $result !== '' ? $result : null;
    }
}
