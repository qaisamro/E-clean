<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class FaqsCategory extends Model
{
    protected $table = 'faq_categories';

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'faq_category_id');
    }
}
