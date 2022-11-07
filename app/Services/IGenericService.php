<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface IGenericService
{
    /**
     * Get one register by filters.
     *
     * @param Request $request
     * @return Model
     */
    public function find(Request $request): Model;

    /**
     * Get all register by filters.
     *
     * @param Request $request
     * @return Collection
     */
    public function findAll(Request $request): Collection;

    /**
     * Store data
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update data
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /**
     * Delete a register
     *
     * @param Model $model
     * @return boolean
     */
    public function delete(Model $model): bool;

    /**
     * Force Delete a register
     *
     * @param int $id
     * @return boolean
     */
    public function forceDelete(int $id): bool;
}
