<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportReportSetting extends Model
{
    protected $fillable = [
        'report_code_suffix',
        'sequence_year',
        'last_sequence',
        'recipient_name',
        'recipient_position',
        'sender_prefix',
        'sender_position',
        'header_image_path',
        'footer_image_path',
    ];

    public function getHeaderImageUrlAttribute(): ?string
    {
        if (!$this->header_image_path) {
            return null;
        }

        return asset('storage/' . $this->header_image_path);
    }

    public function getFooterImageUrlAttribute(): ?string
    {
        if (!$this->footer_image_path) {
            return null;
        }

        return asset('storage/' . $this->footer_image_path);
    }
}
