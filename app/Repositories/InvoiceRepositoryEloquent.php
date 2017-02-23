<?php

namespace App\Repositories;

use App\Models\Invoice;
use Sentinel;

class InvoiceRepositoryEloquent implements InvoiceRepository
{
    /**
     * @var Invoice
     */
    private $model;
    private $user;


    /**
     * InvoiceRepositoryEloquent constructor.
     * @param Invoice $model
     */
    public function __construct(Invoice $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}