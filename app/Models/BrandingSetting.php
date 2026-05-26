<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandingSetting extends Model
{
    protected $fillable = [
        'navbar_logo_path',
        'login_image_path',
    ];

    public function getNavbarLogoUrlAttribute(): ?string
    {
        if (!$this->navbar_logo_path) {
            return null;
        }

        return asset('storage/' . $this->navbar_logo_path);
    }

    public function getLoginImageUrlAttribute(): ?string
    {
        if (!$this->login_image_path) {
            return null;
        }

        return asset('storage/' . $this->login_image_path);
    }
}
