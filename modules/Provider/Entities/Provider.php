<?php

namespace Modules\Provider\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Provider extends Model {
    use HasFactory;

    protected $fillable = ['name', 'class_name', 'end_point', 'api_key', 'fetched_at'];

    // Ensure api_key is always stored encrypted
    public function setApiKeyAttribute($value): void {
        if ($value === null || $value === '') {
            $this->attributes['api_key'] = $value;
            return;
        }
        try {
            // If it's already encrypted, decrypt will succeed → keep original encrypted value
            Crypt::decrypt($value);
            $this->attributes['api_key'] = $value;
        } catch (\Throwable $e) {
            // Not encrypted → encrypt before storing
            $this->attributes['api_key'] = Crypt::encrypt($value);
        }
    }
}
