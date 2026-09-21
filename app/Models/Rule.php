<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'event_trigger',
        'is_active',
    ];

    public function conditions()
    {
        return $this->hasMany(RuleCondition::class);
    }

    public function actions()
    {
        return $this->hasMany(RuleAction::class);
    }
}
