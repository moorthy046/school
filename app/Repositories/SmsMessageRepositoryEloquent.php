<?php

namespace App\Repositories;

use App\Models\SmsMessage;
use Sentinel;

class SmsMessageRepositoryEloquent implements SmsMessageRepository
{
    /**
     * @var SmsMessage
     */
    private $model;
    private $user;


    /**
     * MarkTypeRepositoryEloquent constructor.
     * @param SmsMessage $model
     */
    public function __construct(SmsMessage $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }

    public function getAllForSender($user_id_sender)
    {
        return $this->model->where('user_id_sender', $user_id_sender);
    }
}