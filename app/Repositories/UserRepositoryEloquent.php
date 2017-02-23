<?php

namespace App\Repositories;

use App\Models\User;
use Cartalyst\Sentinel\Sentinel;
use Illuminate\Support\Collection;

class UserRepositoryEloquent implements UserRepository
{
    /**
     * @var User
     */
    private $model;
    private $user;
    private $sentinel;


    /**
     * SUserRepositoryEloquent constructor.
     * @param User $model
     */
    public function __construct(User $model,Sentinel $sentinel)
    {
        $this->model = $model;
        $this->sentinel = $sentinel;
        $this->user = $this->sentinel->getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getUsersForRole($role)
    {
        $users = new Collection([]);
        $this->model->get()
            ->each(function ($user) use ($users) {
                $users->push($user);
            });
        $users = $users->filter(function ($user) use ($role) {
            return $user->inRole($role);
        });
        return $users;
    }
}