<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Process;

final readonly class Git
{
    /**
     * Get the current version of the site.
     */
    public function getLatestTag(): string
    {
        return trim(
            Process::path(base_path())->run(['git', 'describe', '--tags', '--abbrev=0'])->output(),
        );
    }

    public function getAppBranch(): string
    {
        //git rev-parse --abbrev-ref HEAD
        return trim(
            Process::path(base_path())->run(['git', 'rev-parse', '--abbrev-ref', 'HEAD'])->output(),
        );
    }

    public function getAppCommitHash(): string
    {
        //git rev-parse --short HEAD
        return trim(
            Process::path(base_path())->run(['git', 'rev-parse', '--short', 'HEAD'])->output(),
        );
    }


    public function getRepoUrl(): ?string
    {
        //git remote get-url origin
        $url = trim(
            Process::path(base_path())->run(['git', 'remote', 'get-url', 'origin'])->output()
        );
        if (
            (str_contains($url, '@') && str_contains($url, ':')) &&
            (str_contains($url, 'github.com') || str_contains($url, 'gitlab.com'))
        ) {
            // SSH URL for GitHub or GitLab
            return $this->sshToHttps($url);
        } elseif (str_starts_with($url, 'https://github.com') || str_starts_with($url, 'https://gitlab.com')) {
            // HTTPS URL from GitHub or GitLab, return as it is
            return $url;
        } else {
            // Unsupported or invalid URL format
            return null;
        }
    }

    private function sshToHttps(string $sshUrl): string
    {

        $parts = explode(':', $sshUrl);

        // Extract username and repository name
        $usernameAndRepo = explode('/', $parts[1]);
        $username = $usernameAndRepo[0];
        $repo = $usernameAndRepo[1];

        // Determine the host (GitHub, GitLab, etc.)
        $host = substr($parts[0], strpos($parts[0], '@') + 1);

        // Construct the HTTPS URL
        return "https://$host/$username/$repo";
    }
}
