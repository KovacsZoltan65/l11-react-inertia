<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
    protected $table = 'projects';
    
    protected $fillable = [
        'name', 'description', 'due_date', 'status', 'image_path'
    ];
    
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
