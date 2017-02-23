<?php

namespace App\Repositories;

use App\Models\MarkType;
use Sentinel;

class MarkTypeRepositoryEloquent implements MarkTypeRepository
{
    /**
     * @var MarkType
     */
    private $model;
    private $user;


    /**
     * MarkTypeRepositoryEloquent constructor.
     * @param MarkType $model
     */
    public function __construct(MarkType $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}