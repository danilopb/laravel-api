<?php
namespace App\Services;

use App\Filters\ApiFilter;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class GenericServiceImpl implements IGenericService
{
    private ApiFilter $apiFilter;
    protected Model $model;

    public function __construct(Model $model, ApiFilter $apiFilter)
    {
        $this->apiFilter = $apiFilter;
        $this->model = $model;
    }

    /**
     * Get one register by id and filters.
     *
     * @param Request $request
     * @return Model
     */
    public function find(Request $request): Model
    {
        $queryItems = $this->apiFilter->transform($request);
        return $this->model->where($queryItems)->first();
    }

    /**
     * Get all register by filters.
     *
     * @param Request $request
     * @return Collection
     */
    public function findAll(Request $request): Collection
    {
        $queryItems = $this->apiFilter->transform($request);
        $relations = $this->apiFilter->getRelations($request);
        return $this->model->where($queryItems)->with($relations)->get();
    }

    /**
     * Store data
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update data
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model;
    }

    /**
     * Delete a register
     *
     * @param Model $model
     * @return boolean
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Force Delete a register
     *
     * @param int $id
     * @return boolean
     */
    public function forceDelete(int $id): bool
    {
        $record = $this->model->where('id',$id)->withTrashed()->first();
        if (!$record) {
            throw new ModelNotFoundException();
        }
        return $record->forceDelete();
    }
}
