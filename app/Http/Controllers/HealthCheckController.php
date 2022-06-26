<?php

namespace App\Http\Controllers;

use App\Services\HealthCheckerService;
use Illuminate\Http\Request;

class HealthCheckController extends Controller
{
    private $checker;
    public function __construct(HealthCheckerService $checker)
    {
        $this->checker = $checker;
    }
    public function liveness()
    {
        return $this->checker->liveness();
    }
    public function readiness()
    {
        return $this->checker->readiness();
    }
}
