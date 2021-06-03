<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest {
    /**
     * Default authorization rules
     *
     * @return array
     */
    public function rules() {
        return [];
    }

    public function getIncludes(): array {
        $with = $this->header('X-with') ?? $this->query->get('with');
        return $with ? $this->getArrayValue($with, ',') : [];
    }

    public function getIncludesWithTrashed(array $trashedModels): array {
        $newIncludes = [];
        foreach ($this->getIncludes() as $include) {
            if (in_array($include, $trashedModels, true)) {
                $newIncludes[$include] = static function($query) {
                    $query->withTrashed();
                };
            } else {
                $newIncludes[] = $include;
            }
        }
        return $newIncludes;
    }

    public function getIncludesOnlyPublished(): array {
        $newIncludes = [];
        foreach ($this->getIncludes() as $include) {
            $newIncludes[$include] = static function($query) {
                $query->where('is_published', true);
            };
        }
        return $newIncludes;
    }

    public function getLimit() {
        return (int)$this->query->get('limit') ?: (int)$this->header('X-limit') ?: config('api.pagination_limit_default');
    }

    public function getPage(): int {
        return (int)$this->query->get('page') ?: (int)$this->header('X-page') ?: 1;
    }

    public function hasSearch(): bool {
        return $this->filled('search');
    }

    public function search() {
        return $this->search;
    }

    public function getOrderBy(): array {
        $options = $this->getArrayValue($this->orderBy, ':');

        return [
            'column'    => $options[0] ?? null,
            'direction' => $options[1] ?? null,
        ];
    }

    public function hasOrderBy(): bool {
        return $this->has('orderBy');
    }


    /**
     * @param string $key
     * @return bool|null
     */
    public function getBoolFromRequest(string $key): ?bool {
        return $this->filled($key) ? $this->getBoolValue($this->get($key)) : null;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function getBoolValue(string $value): bool {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }


    /**
     * @param string $key
     * @param string $delimiter
     * @return array
     */
    public function getArrayFromRequest(string $key, string $delimiter = ','): array {
        return $this->filled($key) ? $this->getArrayValue($this->get($key), $delimiter) : [];
    }


    /**
     * @param string $value
     * @param string $delimiter
     * @return array
     */
    private function getArrayValue(string $value, string $delimiter = ','): array {
        return array_map('trim', explode($delimiter, $value));
    }


    public function getValidatorKeys(): array {
        return array_keys($this->rules());
    }

}
