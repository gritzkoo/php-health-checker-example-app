<?php

namespace App\Services;

use App\Api\FakeApi1\FakeApi1;
use App\Api\FakeApi2\FakeApi2;
use Gritzkoo\HealthChecker\Check;
use Gritzkoo\HealthChecker\HealthChecker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class HealthCheckerService extends HealthChecker
{
    public function __construct(
        FakeApi1 $fakeApi1,
        FakeApi2 $fakeApi2
    ) {
        parent::__construct([
            'name' => 'laravel test',
            'version' => 'v1.0.1',
            'integrations' => [
                [
                    'name'   => 'Fake API integration 1',
                    'handle' => function () use ($fakeApi1) {
                        return $fakeApi1->check();
                    }
                ],
                [
                    'name'   => 'Fake API integration 2',
                    'handle' => function () use ($fakeApi2) {
                        return $fakeApi2->check();
                    }
                ],
                [
                    'name'   => 'Redis validation',
                    'handle' => function () {
                        $check = new Check([
                            'url' => env('REDIS_HOST') . ':' . env('REDIS_PORT')
                        ]);
                        try {
                            $data = Cache::add('health-checker', 'validation', 10);
                        } catch (\Throwable $th) {
                            $check->error = $th;
                        }
                        if (!$data) {
                            $check->error = 'Cache add return false';
                        }
                        return $check;
                    }
                ],
                [
                    'name'   => 'Session validation',
                    'handle' => function () {
                        $check = new Check([
                            'url' => env('SESSION_DRIVER')
                        ]);
                        try {
                            $data = Session::all();
                        } catch (\Throwable $th) {
                            $check->error = $th;
                        }
                        if (!$data) {
                            $check->error = 'Session return nothing';
                        }
                        return $check;
                    }
                ],
                [
                    'name'   => 'Database connection',
                    'handle' => function () {
                        $check = new Check([
                            'url' => env('DB_CONNECTION')
                                . "://"
                                . env('DB_HOST')
                                . ":"
                                . env('DB_PORT')
                        ]);
                        try {
                            DB::connection()->getPDO();
                        } catch (\Throwable $th) {
                            $check->error = $th;
                        }
                        return $check;
                    }
                ],
            ]
        ]);
    }
    public function readiness()
    {
        $result = parent::readiness();
        if (!$result['status']) {
            Log::warning("Health check fails", $result);
        }
        return $result;
    }
}
