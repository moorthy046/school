<?php

namespace App\Repositories;

use App\Models\BookUser;
use Sentinel;

class BookUserRepositoryEloquent implements BookUserRepository
{
    /**
     * @var Book
     */
    private $model;
    private $user;


    /**
     * BookUserRepositoryEloquent constructor.
     * @param BookUser $model
     */
    public function __construct(BookUser $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}