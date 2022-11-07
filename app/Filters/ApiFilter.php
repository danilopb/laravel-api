<?php

namespace App\Filters;


use Illuminate\Http\Request;

class ApiFilter
{
    protected array $safeParams = [];
    protected array $safeRelations = [];
    protected array $columnMap = [];
    protected array $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'lki' => '%word',
        'lkf' => 'word%',
        'lka' => '%word%'
    ];

    public function transform(Request $request): array
    {
        $eloQuery = [];

        foreach ($this->safeParams as $param => $operators) {
            $query = $request->query($param);
            if (!isset($query)) {
                continue;
            }
            $column = $this->columnMap[$param] ?? $param;
            foreach ($operators as $operator) {
                $valueSearched = $query[$operator] ?? null;
                if ($valueSearched) {
                    switch ($operator) {
                        case 'lk':
                            $eloQuery[] = [$column, 'like', '%'.$valueSearched.'%'];
                            break;
                        default:
                            $eloQuery[] = [$column, $this->operatorMap[$operator], $valueSearched];
                    }
                }
            }
        }
        return $eloQuery;
    }

    public function getRelations(Request $request) : array
    {
        $relations = [];
        foreach ($this->safeRelations as $relation) {
            $queryKey = 'includes'.ucfirst($relation);
            $query = $request->query($queryKey);
            if (isset($query) && filter_var($query, FILTER_VALIDATE_BOOLEAN)) {
                $relations[] = $relation;
            }
        }
        return $relations;
    }
}
