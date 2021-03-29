<?php
/**
 * Created date 18.01.2021
 * @author Sergey Tyrgola <ts@GoldcarrotLaravel\Base.ru>
 */

namespace GoldcarrotLaravel\Base\Presenters;

use GoldcarrotLaravel\Base\Interfaces\PresenterInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BasePresenter implements PresenterInterface
{
    protected Model $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    abstract public function toArray(): array;

    public function toPublicArray(): array
    {
        return $this->toArray();
    }
}
