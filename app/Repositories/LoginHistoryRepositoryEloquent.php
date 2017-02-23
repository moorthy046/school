<?php

namespace App\Repositories;

use App\Models\LoginHistory;
use Sentinel;

class LoginHistoryRepositoryEloquent implements LoginHistoryRepository
{
    /**
     * @var LoginHistory
     */
    private $model;
    private $user;


    /**
     * LoginHistoryRepositoryEloquent constructor.
     * @param LoginHistory $model
     */
    public function __construct(LoginHistory $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}