<?php namespace App\Repositories;

use App\Models\Option;
use Sentinel;

class OptionRepositoryEloquent implements OptionRepository
{
    /**
     * @var Option
     */
    private $model;
    private $user;

    /**
     * OptionRepositoryEloquent constructor.
     * @param Option $model
     */
    public function __construct(Option $model)
    {

        $this->model = $model;
        $this->user = Sentinel::getUser();
    }

    public function getAll()
    {
        return $this->model->where('school_id', 0);
    }

    public function getAllForSchool($school_id)
    {
        return $this->model->where(function($query) use ($school_id) {
            return $query->where('school_id', $school_id)
                ->orWhere('school_id', 0);
        });
    }
}