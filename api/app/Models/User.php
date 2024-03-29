<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use App\Scopes\User as Scope;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
     * The "booted" method of the model
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new Scope);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_activated', 1);
    }

    public function forecast()
    {
        return $this->hasOne(UserWeather::class, 'user_id', 'id');
    }
}
