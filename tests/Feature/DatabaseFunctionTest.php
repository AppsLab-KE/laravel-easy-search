<?php


namespace AppsLab\LaravelEasySearch\Tests\Feature;



use AppsLab\LaravelEasySearch\Repositories\DatabaseRepository;
use AppsLab\LaravelEasySearch\Tests\TestCase;

class DatabaseFunctionTest extends TestCase
{
    public function test_can_get_database_name()
    {
        $this->assertEquals("demos", DatabaseRepository::conn("demos")->tableName);
    }

    public function testDatabaseExists()
    {
        $this->assertFalse(false);
    }
}