<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appearance = $request->cookie('appearance') ?? 'system';
        $presetCookie = $request->cookie('theme_preset');

        $fontSize = '16px';
        $designStyle = 'nova';
        $designBaseColor = 'neutral';
        $hasRadiusOverride = false;

        if ($presetCookie && is_string($presetCookie) && str_starts_with($presetCookie, 'ex_')) {
            $b64 = substr($presetCookie, 3);
            $mod4 = strlen($b64) % 4;
            $padded = $mod4 === 0 ? $b64 : $b64.str_repeat('=', 4 - $mod4);
            $decoded = base64_decode(strtr($padded, '-_', '+/'), true);

            if ($decoded !== false) {
                $payload = json_decode($decoded, true);

                if (is_array($payload)) {
                    if (! empty($payload['fs'])) {
                        $fs = $payload['fs'];

                        if (is_numeric($fs)) {
                            $fontSize = match ((int) $fs) {
                                75 => '12px',
                                125 => '20px',
                                150 => '24px',
                                default => '16px',
                            };
                        } elseif ($fs === 'sm') {
                            $fontSize = '12px';
                        } elseif ($fs === 'lg') {
                            $fontSize = '20px';
                        }
                    }

                    if (! empty($payload['s']) && is_string($payload['s'])) {
                        $designStyle = $payload['s'];
                    }

                    if (! empty($payload['b']) && is_string($payload['b'])) {
                        $designBaseColor = $payload['b'];
                    }

                    if (array_key_exists('r', $payload) && $payload['r'] !== null) {
                        $hasRadiusOverride = true;
                    }
                }
            }
        }

        View::share('appearance', $appearance);
        View::share('fontSize', $fontSize);
        View::share('designStyle', $designStyle);
        View::share('designBaseColor', $designBaseColor);
        View::share('hasRadiusOverride', $hasRadiusOverride);

        return $next($request);
    }
}
