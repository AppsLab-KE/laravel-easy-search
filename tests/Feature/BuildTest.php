<?php


namespace AppsLab\LaravelEasySearch\Tests\Feature;


use AppsLab\LaravelEasySearch\Repositories\BuildRepository;
use AppsLab\LaravelEasySearch\Tests\TestCase;

class BuildTest extends TestCase
{
    public function testThatCanBuild()
    {
        $response = BuildRepository::build()->query("Marvin");

        $this->assertArrayHasKey("condition", $response);
        $this->assertArrayHasKey("query", $response);
    }
}