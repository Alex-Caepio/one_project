<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    /**
     * Default authorization rules
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    public function getIncludes()
    {
        $with = $this->header('X-with') ?? $this->query->get('with');

        return $with
            ? array_map('trim', explode(',', $with))
            : [];
    }

    public function getLimit()
    {
        return (int)$this->query->get('limit')
            ?: (int)$this->header('X-limit')
                ?: config('api.pagination_limit_default');
    }

    public function getPage(): int
    {
        return (int)$this->query->get('page')
            ?: (int)$this->header('X-page') ?: 1;
    }

    public function hasSearch(): bool
    {
        return $this->filled('search');
    }

    public function search()
    {
        return $this->search;
    }

    public function getOrderBy(): array
    {
        $options = explode(':', $this->orderBy);

        return [
            'column'    => $options[0] ?? null,
            'direction' => $options[1] ?? null,
        ];
    }

    public function hasOrderBy(): bool
    {
        return $this->has('orderBy');
    }

}
