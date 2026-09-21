<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_id',
        'action_type',
        'action_params',
    ];

    protected $casts = [
        'action_params' => 'array',
    ];

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}
