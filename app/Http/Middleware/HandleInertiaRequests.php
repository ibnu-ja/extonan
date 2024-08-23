<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'ziggy' => fn() => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'appVersion' => exec('git describe --tags --abbrev=0'),
            'appCommitHash' => exec('git rev-parse --short HEAD'),
            'appBranch' => exec('git rev-parse --abbrev-ref HEAD'),
            'appGitOriginRepo' => $this->printRepoUrl(exec('git remote get-url origin')),
        ];
    }


    private function printRepoUrl(string $url): ?string
    {
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
