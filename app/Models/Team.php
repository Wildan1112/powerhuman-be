<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'icon', 'company_id'
    ];

    // relasi team-company
    public function company()
    {
        return $this->belongsTo(Comapny::class);
    }

    // relasi team-employee
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
