<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $guarded = array('id');

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function setSubjectIdAttribute($subject_id)
    {
        if ($subject_id) {
            $this->attributes['subject_id'] = $subject_id;
        } else {
            $this->attributes['subject_id'] = NULL;
        }
    }

    public function availableCopies()
    {
        $issued = BookUser::where('book_id', $this->attributes['id'])
            ->whereNotNull('get')
            ->whereNull('back')
            ->count();
        return $this->attributes['quantity'] - $issued;
    }
}
