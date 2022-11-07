<?php

namespace App\Filters;

class PostFilter extends ApiFilter
{
    protected array $safeParams = [
        'title' => ['eq', 'lk'],
        'content' => ['eq', 'lk'],
        'created_at' => ['eq', 'gt', 'gte', 'lt', 'lte']
    ];
    protected array $safeRelations = ['user'];
    protected array $columnMap = [];
    protected array $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'lk' => '%word%',
    ];
}
