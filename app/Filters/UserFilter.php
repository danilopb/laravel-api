<?php

namespace App\Filters;

class UserFilter extends ApiFilter
{
    protected array $safeParams = [
        'names' => ['eq'],
        'surnames' => ['eq'],
        'email' => ['eq']
    ];
    protected array $safeRelations = ['posts'];
    protected array $columnMap = [];
    protected array $operatorMap = [
        'eq' => '='
    ];
}
