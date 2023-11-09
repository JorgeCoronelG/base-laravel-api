<?php

namespace App\Core;

use App\Core\Contracts\BaseRepositoryInterface;
use App\Core\Contracts\BaseServiceInterface;
use App\Exceptions\CustomErrorException;
use App\Helpers\Enum\QueryParam;
use App\Helpers\Validation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Data;

class BaseService implements BaseServiceInterface
{
    protected BaseRepositoryInterface $entityRepository;

    public function create(Data $data): Model
    {
        return $this->entityRepository->create($data->toArray());
    }

    public function delete(int $id): void
    {
        $this->entityRepository->delete($id);
    }

    public function findAll(
        array $filter = [],
        string $sort = null,
        array $columns = ['*']
    ): Collection {
        return $this->entityRepository->findAll($filter, $sort, $columns);
    }

    /**
     * @throws CustomErrorException
     */
    public function findAllPaginated(
        Request $request,
        array $columns = ['*']
    ): LengthAwarePaginator {
        $filters = Validation::getFilters($request->get(QueryParam::FILTERS_KEY));
        $perPage = Validation::getPerPage($request->get(QueryParam::PAGINATION_KEY));
        $sort = $request->get(QueryParam::ORDER_BY_KEY);
        return $this->entityRepository->findAllPaginated($filters, $perPage, $sort, $columns);
    }

    public function findById(int $id, array $columns = ['*']): Model
    {
        return $this->entityRepository->findById($id, $columns);
    }

    public function findRandom(): Model
    {
        return $this->entityRepository->findRandom();
    }

    public function findRandoms(int $records = 1): Collection
    {
        return $this->entityRepository->findRandoms($records);
    }

    public function update(int $id, Data $data): Model
    {
        return $this->entityRepository->update($id, $data->toArray());
    }
}
