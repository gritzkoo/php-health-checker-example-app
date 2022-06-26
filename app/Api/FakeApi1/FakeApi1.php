<?php

namespace App\Api\FakeApi1;

use App\Api\BaseApi;
use Gritzkoo\HealthChecker\Check;
use Illuminate\Support\Facades\Http;

class FakeApi1 extends BaseApi
{

    public function __construct()
    {
        $this->baseUrl = env('FAKE_API1_HOST', 'http://api1:8080');
        $this->configFile = app_path('Api/FakeApi1/config.php');
    }
    public function check(): Check
    {
        $check = new Check([
            'url' => $this->getRoute('status')
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
