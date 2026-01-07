<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ButuanOrdinance extends Model
{
    protected $fillable = [
        'ordinance_number',
        'title',
        'legal_category_id',
        'summary',
        'status',
        'repealed_by',
        'file_path',
        'year',
    ];

    public function category()
    {
        return $this->belongsTo(LegalCategory::class, 'legal_category_id');
    }
}
