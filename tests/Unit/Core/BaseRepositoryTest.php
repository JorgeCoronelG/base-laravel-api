<?php

namespace Tests\Unit\Core;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Support\CreatesItemsTable;
use Tests\Support\Item;
use Tests\Support\ItemRepository;
use Tests\TestCase;

/**
 * Tests del contrato de BaseRepository.
 */
class BaseRepositoryTest extends TestCase
{
    use CreatesItemsTable;

    private ItemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createItemsTable();
        $this->repository = new ItemRepository();
    }

    public function test_create_persists_and_returns_the_model(): void
    {
        $item = $this->repository->create(['name' => 'Ana', 'status' => 1]);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertTrue($item->exists);
        $this->assertDatabaseHas('items', ['name' => 'Ana', 'status' => 1]);
    }

    public function test_update_fills_and_persists(): void
    {
        $item = $this->repository->create(['name' => 'Ana', 'status' => 1]);

        $updated = $this->repository->update($item->id, ['name' => 'Ana María']);

        $this->assertSame('Ana María', $updated->name);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'Ana María', 'status' => 1]);
    }

    public function test_update_of_missing_id_throws_model_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->update(999, ['name' => 'X']);
    }

    public function test_delete_removes_the_record(): void
    {
        $item = $this->repository->create(['name' => 'Ana']);

        $this->repository->delete($item->id);

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_delete_of_missing_id_throws_model_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->delete(999);
    }

    public function test_find_by_id_returns_the_model_or_throws(): void
    {
        $item = $this->repository->create(['name' => 'Ana']);

        $this->assertSame($item->id, $this->repository->findById($item->id)->id);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findById(999);
    }

    public function test_find_by_id_respects_selected_columns(): void
    {
        $item = $this->repository->create(['name' => 'Ana', 'status' => 1]);

        $found = $this->repository->findById($item->id, ['id', 'name']);

        $this->assertArrayNotHasKey('status', $found->getAttributes());
    }

    public function test_find_all_returns_everything_without_filters(): void
    {
        $this->seedItems();

        $this->assertCount(3, $this->repository->findAll());
    }

    public function test_find_all_paginated(): void
    {
        $this->seedItems();

        $page = $this->repository->findAllPaginated([], 2);

        $this->assertSame(3, $page->total());
        $this->assertCount(2, $page->items());
        $this->assertSame(2, $page->lastPage());
    }

    public function test_find_random_returns_a_record(): void
    {
        $this->seedItems();

        $this->assertInstanceOf(Item::class, $this->repository->findRandom());
        $this->assertCount(2, $this->repository->findRandoms(2));
    }

    public function test_find_random_on_empty_table_throws_model_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->findRandom();
    }

    public function test_bulk_insert(): void
    {
        $result = $this->repository->bulkInsert([
            ['name' => 'A', 'status' => 1],
            ['name' => 'B', 'status' => 1],
        ]);

        $this->assertTrue($result);
        $this->assertDatabaseCount('items', 2);
        // insert() no rellena timestamps.
        $this->assertNull(Item::first()->created_at);
    }

    public function test_bulk_update_returns_affected_rows(): void
    {
        $this->seedItems();

        $affected = $this->repository->bulkUpdate([1, 2], ['status' => 9]);

        $this->assertSame(2, $affected);
        $this->assertSame(2, Item::where('status', 9)->count());
    }

    public function test_bulk_delete_returns_deleted_rows_and_deletes(): void
    {
        $this->seedItems();

        $this->assertSame(2, $this->repository->bulkDelete([1, 2]));
        $this->assertDatabaseCount('items', 1);
    }
}
