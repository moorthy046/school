<?php

namespace App\Repositories;

use App\Models\Feedback;
use Sentinel;

class FeedbackRepositoryEloquent implements FeedbackRepository
{
    /**
     * @var Feedback
     */
    private $model;
    private $user;


    /**
     * FeedbackRepositoryEloquent constructor.
     * @param Feedback $model
     */
    public function __construct(Feedback $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}