<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Todo
 * @package App\Models
 * @property int user_id
 * @property string title
 * @property string description
 * @property boolean is_done
 */
class Todo extends Model
{
    use SoftDeletes;

    protected $table = 'todo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'is_done'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
