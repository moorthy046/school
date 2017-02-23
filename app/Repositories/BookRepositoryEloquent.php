<?php

namespace App\Repositories;

use App\Models\Book;
use Sentinel;

class BookRepositoryEloquent implements BookRepository
{
    /**
     * @var Book
     */
    private $model;
    private $user;


    /**
     * BookRepositoryEloquent constructor.
     * @param Book $model
     */
    public function __construct(Book $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}