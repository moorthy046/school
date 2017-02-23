<?php

namespace App\Repositories;

use App\Models\Notification;
use Sentinel;

class NotificationRepositoryEloquent implements NotificationRepository
{
    /**
     * @var Notification
     */
    private $model;
    private $user;


    /**
     * NotificationRepositoryEloquent constructor.
     * @param Notification $model
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}