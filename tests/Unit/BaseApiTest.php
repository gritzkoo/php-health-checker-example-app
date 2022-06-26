<?php

namespace Tests\Unit;

use App\Api\BaseApi;
use Closure;
use PHPUnit\Framework\TestCase;
use Exception;

class BaseApiTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     * @dataProvider baseApiProvider
     */
    public function testBaseApi(Closure $testSuite): void
    {
        $scenario = $testSuite();
        if ($scenario['throws'] ?? false) {
            $this->expectException($scenario['throw_class']);
            $this->expectExceptionMessage($scenario['throw_message']);
        }
        $inst = new StubTestBaseApi($scenario['baseUrl'], $scenario['configFile']);
        $result = $inst->route($scenario['routeName']);
        $this->assertEquals($scenario['expected'], $result);
    }

    public function baseApiProvider()
    {
        return [
            'should create ok' => [
                function () {
                    return [
                        'baseUrl' => 'test',
                        'configFile' => 'tests/test-base-api-config.php',
                        'routeName' => 'test',
                        'expected' => 'test/test'
                    ];
                }
            ],
            'should throw error because the file does not exists' => [
                function () {
                    return [
                        'throws' => true,
                        'throw_class' => Exception::class,
                        'throw_message' => 'The config file not found tests/test-base-api-configs.php',
                        'baseUrl' => 'test',
                        'configFile' => 'tests/test-base-api-configs.php',
                        'routeName' => 'test',
                        'expected' => 'test/test'
                    ];
                }
            ],
            'should throw error because array key does not exists' => [
                function () {
                    return [
                        'throws' => true,
                        'throw_class' => Exception::class,
                        'throw_message' => 'key not-found not found in tests/test-base-api-config.php',
                        'baseUrl' => 'test',
                        'configFile' => 'tests/test-base-api-config.php',
                        'routeName' => 'not-found',
                        'expected' => 'test/test'
                    ];
                }
            ],
            'should throw error because route name return a nom string value' => [
                function () {
                    return [
                        'throws' => true,
                        'throw_class' => Exception::class,
                        'throw_message' => 'The navigation in config file return a non string value',
                        'baseUrl' => 'test',
                        'configFile' => 'tests/test-base-api-config.php',
                        'routeName' => 'some-other-values',
                        'expected' => 'test/test'
                    ];
                }
            ],
        ];
    }
}

class StubTestBaseApi extends BaseApi
{
    public function __construct(string $baseUrl, string $configFile)
    {
        $this->configFile = $configFile;
        $this->baseUrl = $baseUrl;
    }
    public function route(string $name)
    {
        return $this->getRoute($name);
    }
}
