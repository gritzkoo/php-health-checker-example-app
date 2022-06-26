<?php

namespace App\Api\FakeApi2;

use App\Api\BaseApi;
use Gritzkoo\HealthChecker\Check;
use Illuminate\Support\Facades\Http;

class FakeApi2 extends BaseApi
{

    public function __construct()
    {
        $this->baseUrl = env('FAKE_API1_HOST', 'http://api2:8080');
        $this->configFile = app_path('Api/FakeApi2/config.php');
    }
    public function check(): Check
    {
        $check = new Check([
            'url' => $this->getRoute('health-check.liveness')
        ]);
        try {
            $response = Http::get($check->url);
        } catch (\Throwable $th) {
            $check->error = $th;
        }
        if ($response->status() != 200) {
            $check->error = [
                'response' => $response->json(),
                'status' => $response->status()
            ];
        }
        return $check;
    }
}
