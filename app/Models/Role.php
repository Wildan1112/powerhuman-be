<?php

namespace App\Models;

use App\Models\Responsibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'company_id'
    ];

    // relasi role->company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // relasi role->responsibility
    public function responsibilities()
    {
       return $this->hasMany(Responsibility::class);
    }

    // relasi role->employee
    public function employees()
    {
       return $this->hasMany(Employee::class);
    }
}
