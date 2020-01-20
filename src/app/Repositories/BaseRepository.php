<?php

namespace App\Repositories;

abstract class BaseRepository
{
    protected $entity;

    public abstract function entity();

    public function __construct()
    {
        $this->entity = $this->resolveEntity();
    }

    private function resolveEntity()
    {
        return app($this->entity());
    }

    public function __call($method, $arguments)
    {
        return \call_user_func_array([$this->entity, $method], $arguments);
    }
}
