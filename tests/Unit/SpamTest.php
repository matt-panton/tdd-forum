<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Inspections\Spam;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpamTest extends TestCase
{
    protected $spam;

    public function setUp()
    {
        $this->spam = new Spam();
    }

    /** @test */
    public function it_checks_for_invalid_keywords()
    {
        $this->assertFalse($this->spam->detect('Innocent reply here,'));
        
        $this->expectException(\Exception::class);

        $this->spam->detect('Yahoo Customer support');
    }

    /** @test */
    public function it_checks_for_key_held_down()
    {
        $this->expectException(\Exception::class);
        
        $this->spam->detect('Hello worldsaaaaaaa');
    }
}
