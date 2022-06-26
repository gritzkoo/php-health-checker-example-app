<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class HealthCheckerServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @dataProvider healthProvider
     */
    public function testHealthCheckerService(Closure $testSuite)
    {
        $scenario = $testSuite();
        $response = $this->get($scenario['route']);
        $response->assertStatus($scenario['status'])->assertJsonStructure($scenario['expected']);
    }

    public function healthProvider()
    {
        return [
            'shoul call liveness OK' => [
                function () {
                    return [
                        'route' => route('health-check.liveness', [], false),
                        'status' => 200,
                        'expected' => [
                            'status',
                            'version'
                        ]
                    ];
                }
            ],
            'shoul call readiness OK' => [
                function () {
                    Http::fake([
                        "http://api1:8080/status" => Http::response([
                            'status' => true
                        ], 200),
                        "http://api2:8080/health-check/liveness" => Http::response([
                            "status" => "fully functional",
                            "version" => "v1.0.1"
                        ], 200)
                    ]);
                    return [
                        'route' => route('health-check.readiness', [], false),
                        'status' => 200,
                        'expected' => [
                            'name',
                            'version',
                            'status',
                            'date',
                            'duration',
                            'integrations' => [
                                [
                                    'name',
                                    'status',
                                    'response_time',
                                    'url',
                                    'error'
                                ]
                            ]
                        ]
                    ];
                }
            ],
            'shoul call readiness with responses 404 and response OK' => [
                function () {
                    Http::fake([
                        "http://api1:8080/status" => Http::response([
                            'status' => false
                        ], 404),
                        "http://api2:8080/health-check/liveness" => Http::response([
                            "status" => false
                        ], 404)
                    ]);
                    return [
                        'route' => route('health-check.readiness', [], false),
                        'status' => 200,
                        'expected' => [
                            'name',
                            'version',
                            'status',
                            'date',
                            'duration',
                            'integrations' => [
                                [
                                    'name',
                                    'status',
                                    'response_time',
                                    'url',
                                    'error'
                                ]
                            ]
                        ]
                    ];
                }
            ],
            'shoul call readiness with errors and return OK' => [
                function () {
                    Http::fake([
                        "http://api1:8080/status" => Http::throw(function () {
                            return new Exception("erro");
                        }),
                        "http://api2:8080/health-check/liveness" =>Http::throw(function () {
                            return new Exception("erro");
                        })
                    ]);
                    return [
                        'route' => route('health-check.readiness', [], false),
                        'status' => 200,
                        'expected' => [
                            'name',
                            'version',
                            'status',
                            'date',
                            'duration',
                            'integrations' => [
                                [
                                    'name',
                                    'status',
                                    'response_time',
                                    'url',
                                    'error'
                                ]
                            ]
                        ]
                    ];
                }
            ],
        ];
    }
}
