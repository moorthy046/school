<?php

namespace App\Repositories;

use App\Models\Payment;
use Sentinel;

class PaymentRepositoryEloquent implements PaymentRepository
{
    /**
     * @var Payment
     */
    private $model;
    private $user;


    /**
     * PaymentRepositoryEloquent constructor.
     * @param Payment $model
     */
    public function __construct(Payment $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}