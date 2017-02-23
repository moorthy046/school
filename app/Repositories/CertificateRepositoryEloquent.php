<?php

namespace App\Repositories;

use App\Models\Certificate;
use Sentinel;

class CertificateRepositoryEloquent implements CertificateRepository
{
    /**
     * @var Certificate
     */
    private $model;
    private $user;


    /**
     * PageRepositoryEloquent constructor.
     * @param Certificate $model
     */
    public function __construct(Certificate $model)
    {
        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model;
    }
}