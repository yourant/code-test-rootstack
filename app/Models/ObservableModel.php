<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservableModel extends Model
{
    protected $fillable = ['action', 'model_id', 'model_type', 'json'];

    public function scopeOfId($query, $id)
    {
        if (is_array($id) && !empty($id)) {
            return $query->whereIn('observable_models.id', $id);
        } else {
            return !$id ? $query : $query->where('observable_models.id', $id);
        }
    }
}
