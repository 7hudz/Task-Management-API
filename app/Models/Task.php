<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'due_date', 'status'];
    protected $casts = [
        'due_date' => 'date',  
    ];
    
    
    public function users()
    {
        
        return $this->belongsToMany(User::class);
    }
}
