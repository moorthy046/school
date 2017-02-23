<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $guarded = array('id');

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id_sender');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id_receiver');
    }

}
