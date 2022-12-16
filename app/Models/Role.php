<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
       return $this->hasMany(Responsibilty::class);
    }

    // relasi role->employee
    public function employees()
    {
       return $this->hasMany(Employee::class);
    }
}
