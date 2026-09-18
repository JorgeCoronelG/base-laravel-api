<?php

namespace Tests\Unit\Core;

use App\Core\Classes\Filter;
use App\Core\Enum\OperatorSql;
use App\Exceptions\CustomErrorException;
use App\Helpers\Validation;
use ErrorException;
use Tests\Support\CreatesItemsTable;
use Tests\Support\Item;
use Tests\TestCase;
use ValueError;

/**
 * Caracterización de Validation::getFilters y del scope AdvancedFilter.
 */
class FiltersTest extends TestCase
{
    use CreatesItemsTable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createItemsTable();
        $this->seedItems(); // Ana(1), Beto(0), Carlos(1)
    }

    private function names(array $filters): array
    {
        return Item::filter($filters)->orderBy('id')->pluck('name')->all();
    }

    // ---- Validation::getFilters ----

    public function test_get_filters_returns_empty_array_when_null(): void
    {
        $this->assertSame([], Validation::getFilters(null));
    }

    public function test_get_filters_parses_json(): void
    {
        $json = json_encode(['filters' => [['field' => 'name', 'value' => 'Ana', 'operator' => '=']]]);

        $filters = Validation::getFilters(urlencode($json));

        $this->assertCount(1, $filters);
        $this->assertSame('name', $filters[0]->field);
        $this->assertSame(OperatorSql::EQUAL, $filters[0]->operator);
    }

    public function test_get_filters_without_filters_key_is_bad_request(): void
    {
        $this->expectException(CustomErrorException::class);
        $this->expectExceptionCode(400);

        Validation::getFilters(json_encode(['x' => 1]));
    }

    public function test_get_filters_without_field_or_operator_is_bad_request(): void
    {
        $this->expectException(CustomErrorException::class);
        $this->expectExceptionCode(400);

        Validation::getFilters(json_encode(['filters' => [['field' => 'name']]]));
    }

    public function test_get_filters_with_unknown_operator_throws_value_error(): void
    {
        // Comportamiento actual: no se traduce a 400, escapa como ValueError (500).
        $this->expectException(ValueError::class);

        Validation::getFilters(json_encode(['filters' => [['field' => 'name', 'value' => 'a', 'operator' => 'nope']]]));
    }

    public function test_bug_get_filters_without_value_key_fails_even_for_is_null(): void
    {
        // BUG: IS NULL / IS NOT NULL no necesitan valor, pero se exige la clave "value".
        $this->expectException(ErrorException::class);

        Validation::getFilters(json_encode(['filters' => [['field' => 'status', 'operator' => 'IS NULL']]]));
    }

    // ---- AdvancedFilter ----

    public function test_operators(): void
    {
        $this->assertSame(['Ana'], $this->names([new Filter('name', 'Ana', OperatorSql::EQUAL)]));
        $this->assertSame(['Beto', 'Carlos'], $this->names([new Filter('name', 'Ana', OperatorSql::NOT_EQUAL)]));
        $this->assertSame(['Carlos'], $this->names([new Filter('name', 'arl', OperatorSql::CONTAIN)]));
        $this->assertSame(['Ana', 'Beto'], $this->names([new Filter('name', 'arl', OperatorSql::NOT_CONTAIN)]));
        $this->assertSame(['Beto'], $this->names([new Filter('name', 'Be', OperatorSql::STARTS_WITH)]));
        $this->assertSame(['Ana'], $this->names([new Filter('name', 'na', OperatorSql::ENDS_WITH)]));
        $this->assertSame(['Ana', 'Carlos'], $this->names([new Filter('status', 1, OperatorSql::GREATER_THAN_OR_EQUAL)]));
        $this->assertSame(['Beto'], $this->names([new Filter('status', 1, OperatorSql::LESS_THAN)]));
        $this->assertSame([], $this->names([new Filter('status', null, OperatorSql::IS_NULL)]));
        $this->assertSame(['Ana', 'Beto', 'Carlos'], $this->names([new Filter('status', null, OperatorSql::NOT_NULL)]));
    }

    public function test_bug_multiple_filters_are_combined_with_or(): void
    {
        // BUG: name = Ana AND status = 0 no devuelve nada; con OR devuelve Ana y Beto.
        $result = $this->names([
            new Filter('name', 'Ana', OperatorSql::EQUAL),
            new Filter('status', 0, OperatorSql::EQUAL),
        ]);

        $this->assertSame(['Ana', 'Beto'], $result);
    }

    public function test_bug_or_filter_is_not_grouped_and_escapes_other_constraints(): void
    {
        // BUG: orWhere sin agrupar rompe cualquier where previo (scopes globales, soft deletes, tenant...).
        $result = Item::where('status', 0)
            ->filter([new Filter('name', 'Ana', OperatorSql::EQUAL)])
            ->orderBy('id')
            ->pluck('name')
            ->all();

        $this->assertSame(['Ana', 'Beto'], $result); // Ana tiene status = 1 y aun así aparece.
    }

    public function test_bug_any_column_can_be_filtered(): void
    {
        // BUG: no existe whitelist de campos filtrables (a diferencia de allowedSorts).
        $this->assertSame(['Ana'], $this->names([new Filter('id', 1, OperatorSql::EQUAL)]));
        $this->assertSame(['Carlos'], $this->names([new Filter('id', 3, OperatorSql::EQUAL)]));
    }
}
