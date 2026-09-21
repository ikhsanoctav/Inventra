<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataRequest extends Model
{
    protected $fillable = ['user_id', 'request_number', 'type', 'title', 'description', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
