<?php

namespace Tests\Unit\Core;

use Illuminate\Http\Request;
use Tests\Support\CreatesItemsTable;
use Tests\Support\ItemData;
use Tests\Support\ItemRepository;
use Tests\Support\ItemService;
use Tests\TestCase;

class BaseServiceTest extends TestCase
{
    use CreatesItemsTable;

    private ItemService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createItemsTable();
        $this->service = new ItemService(new ItemRepository());
    }

    public function test_create_from_data_object(): void
    {
        $item = $this->service->create(new ItemData('Ana', 1));

        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'Ana', 'status' => 1]);
    }

    public function test_update_from_data_object(): void
    {
        $item = $this->service->create(new ItemData('Ana', 1));

        $this->service->update($item->id, new ItemData('Ana María', 2));

        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'Ana María', 'status' => 2]);
    }

    public function test_bug_update_overwrites_unsent_fields_with_null(): void
    {
        // BUG: Data::toArray() incluye los campos no enviados como null y pisan lo guardado.
        $item = $this->service->create(new ItemData('Ana', 5));

        $this->service->update($item->id, new ItemData(name: 'Nuevo', status: null));

        $this->assertNull($item->fresh()->status);
    }

    public function test_delete_and_find_by_id(): void
    {
        $item = $this->service->create(new ItemData('Ana'));

        $this->assertSame('Ana', $this->service->findById($item->id)->name);

        $this->service->delete($item->id);
        $this->assertDatabaseCount('items', 0);
    }

    public function test_find_all_paginated_reads_query_params_from_request(): void
    {
        $this->seedItems();
        $filters = json_encode(['filters' => [['field' => 'status', 'value' => 1, 'operator' => '=']]]);

        $request = Request::create('/items', 'GET', [
            'q' => $filters,
            'per_page' => 1,
            'sort' => '-name',
        ]);

        $page = $this->service->findAllPaginated($request);

        $this->assertSame(2, $page->total());
        $this->assertSame(1, $page->perPage());
        $this->assertSame('Carlos', $page->items()[0]->name);
    }

    public function test_find_all_paginated_uses_default_page_size(): void
    {
        $this->seedItems();

        $page = $this->service->findAllPaginated(Request::create('/items'));

        $this->assertSame(5, $page->perPage());
    }
}
