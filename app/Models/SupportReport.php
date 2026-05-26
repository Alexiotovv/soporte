<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportReport extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'report_code',
        'report_sequence',
        'report_year',
        'report_date',
        'recipient_name',
        'recipient_position',
        'sender_name',
        'sender_position',
        'subject',
        'content',
        'header_image_path',
        'footer_image_path',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
