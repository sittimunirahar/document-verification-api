<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVerificationResult extends Model
{
    use HasFactory;

    protected $table = 'verification_results';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'file_type',
        'result',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
