<?php

namespace App\Repositories;


interface SubjectRepository
{
    public function getAll();

    public function getAllForDirectionAndClass($direction_id, $class);
}