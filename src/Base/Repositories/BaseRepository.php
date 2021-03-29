<?php

namespace GoldcarrotLaravel\Base\Repositories;


use GoldcarrotLaravel\Base\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class BaseRepository implements RepositoryInterface
{
    protected string $class = Model::class;
    protected int $defaultLimit = 20;
    protected string $keyName = 'id';
    protected string $aliasKeyName = 'id';

    protected array $searchFields = [];

    protected function prepareColumnName($column): string
    {
        return app($this->class)->qualifyColumn($column);
    }

    protected function aliasOrId(Builder $query, $key, $aliasKeyName = 'alias', $keyName = 'id'): Builder
    {
        $keyName = $this->prepareColumnName($keyName);
        $aliasKeyName = $this->prepareColumnName($aliasKeyName);


        $query->where(function (Builder $query) use ($key, $keyName, $aliasKeyName) {
            if (is_numeric($key)) {

                $query
                    ->where($keyName, $key)
                    ->orWhere($aliasKeyName, $key);
            } else {
                $query->where($aliasKeyName, $key);
            }
        });


        return $query;
    }

    protected function query(): Builder
    {
        return (new $this->class)->query();
    }

    protected function active(): Builder
    {
        return $this->query();
    }


    private function byKey(Builder $query, $key): Builder
    {
        return $this->aliasOrId($query, $key, $this->aliasKeyName, $this->keyName);
    }

    public function one($id): Model|Builder|null
    {
        return $this->byKey($this->query(), $id)->first();
    }

    public function all(): Collection|array
    {
        return $this->query()->get();
    }

    public function oneActive($id): Model|Builder|null
    {
        return $this->byKey($this->active(), $id)->first();
    }

    public function allActive(): Collection|array
    {
        return $this->active()->get();
    }

    protected function withParams(Builder $query, array $params = []): Builder
    {
        foreach ($params as $attribute => $value) {
            $attribute = Str::snake($attribute);
            if ($operator = Arr::get($this->searchFields, $attribute)) {

                if (in_array($operator, ['=', '<', '>', '<=', '>=', '<>', '!=', '<=>'])) {
                    $query->where($attribute, $operator, $value);
                }

                if ($operator === 'like') {
                    $query->where($attribute, $operator, "%$value%");
                    break;
                }
            }
        }
        return $query;
    }

    public function paginate(array $params = [], int $limit = null): LengthAwarePaginatorInterface|LengthAwarePaginator
    {
        return $this->withParams($this->query(), $params)->paginate($limit ?: $this->defaultLimit);
    }

    public function paginateActive(array $params = [], int $limit = null): LengthAwarePaginatorInterface|LengthAwarePaginator
    {
        return $this->withParams($this->active(), $params)->paginate($limit ?: $this->defaultLimit);
    }

    public function search(array $params = [], $active = false): Collection|array
    {
        return $this->withParams($active ? $this->active() : $this->query(), $params)->get();
    }

    public function searchActive(array $params = []): Collection|array
    {
        return $this->search($params, true);
    }

    public function find(array $params = [], $active = false): Model|Builder|null
    {
        return $this->withParams($active ? $this->active() : $this->query(), $params)->first();
    }

    public function findActive(array $params = []): Model|Builder|null
    {
        return $this->find($params, true);
    }
}
