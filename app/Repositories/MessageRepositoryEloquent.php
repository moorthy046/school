<?php

namespace App\Repositories;

use App\Models\Message;
use Sentinel;

class MessageRepositoryEloquent implements MessageRepository
{
    /**
     * @var Message
     */
    private $model;
    private $user;


    /**
     * SMessageRepositoryEloquent constructor.
     * @param Message $model
     */
    public function __construct(Message $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}