<?php

namespace App\Repositories;

use App\Models\MarkValue;
use Sentinel;

class MarkValueRepositoryEloquent implements MarkValueRepository
{
    /**
     * @var MarkValue
     */
    private $model;
    private $user;


    /**
     * MarkValueRepositoryEloquent constructor.
     * @param MarkValue $model
     */
    public function __construct(MarkValue $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}