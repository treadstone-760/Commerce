<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment([
            'local',
            'testing',
            'staging',
            'development',
            'dev',
            'production',
            'prod',
        ]);

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Configure the Telescope authorization services.
     */
    protected function authorization(): void
    {
        $this->gate();

        Telescope::auth(function (Request $request) {
            if ($this->app->environment(['local', 'testing'])) {
                return true;
            }

            if ($this->requestHostIsAllowed($request)) {
                return true;
            }

            $user = $request->user();

            return $user !== null && Gate::forUser($user)->check('viewTelescope');
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments
     * when the request host is not on the allowed-host list.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function (User $user) {
            return in_array($user->email, [
                'kboahene760@gmail.com',
            ], true);
        });
    }
    

    protected function requestHostIsAllowed(Request $request): bool
    {
        $allowedHosts = config('telescope.allowed_hosts', []);

        if ($allowedHosts === []) {
            return false;
        }

        $host = $request->getHost();

        return collect($allowedHosts)->contains(
            fn (string $pattern): bool => $this->hostMatchesPattern($host, $this->normalizeHost($pattern))
        );
    }

    protected function hostMatchesPattern(string $host, string $pattern): bool
    {
        if ($pattern === $host) {
            return true;
        }

        if (str_starts_with($pattern, '*.')) {
            $baseDomain = substr($pattern, 2);

            return $baseDomain !== '' && ($host === $baseDomain || str_ends_with($host, '.'.$baseDomain));
        }

        return false;
    }

    protected function normalizeHost(string $value): string
    {
        $value = trim($value);

        if (str_contains($value, '://')) {
            return parse_url($value, PHP_URL_HOST) ?: $value;
        }

        return $value;
    }
}
