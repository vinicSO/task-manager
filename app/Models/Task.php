<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'status'])]
#[Table(name: 'tasks', key: 'id')]
class Task extends Model {

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'task_id', 'id');
    }
}
