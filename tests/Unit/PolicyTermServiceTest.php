<?php

namespace Tests\Unit;

use App\Models\PolicyTerm;
use App\Services\PolicyTermService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Verifies PolicyTermService returns all terms ordered by their display order. */
class PolicyTermServiceTest extends TestCase
{
    use RefreshDatabase;

    private PolicyTermService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PolicyTermService();
    }

    private function term(int $order): array
    {
        return [
            'order'    => $order,
            'title_ar' => "بند {$order}",
            'title_en' => "Clause {$order}",
            'body_ar'  => 'نص',
            'body_en'  => 'Body',
        ];
    }

    public function test_get_all_returns_terms_ordered_by_order_column(): void
    {
        PolicyTerm::create($this->term(2));
        PolicyTerm::create($this->term(1));

        $result = $this->service->getAll();

        $this->assertCount(2, $result);
        $this->assertSame(1, $result->first()['order']);
        $this->assertSame(2, $result->last()['order']);
    }

    public function test_get_all_returns_empty_collection_when_no_terms_exist(): void
    {
        $result = $this->service->getAll();

        $this->assertCount(0, $result);
    }
}
