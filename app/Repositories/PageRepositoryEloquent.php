<?php

namespace App\Repositories;

use App\Models\Page;
use Sentinel;

class PageRepositoryEloquent implements PageRepository
{
    /**
     * @var Page
     */
    private $model;
    private $user;


    /**
     * PageRepositoryEloquent constructor.
     * @param Page $model
     */
    public function __construct(Page $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}