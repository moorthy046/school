<?php

namespace App\Repositories;

use App\Models\NoticeType;
use Sentinel;

class NoticeTypeRepositoryEloquent implements NoticeTypeRepository
{
    /**
     * @var NoticeType
     */
    private $model;
    private $user;


    /**
     * NoticeTypeRepositoryEloquent constructor.
     * @param NoticeType $model
     */
    public function __construct(NoticeType $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}