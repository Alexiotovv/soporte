<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
// use App\Rules\FileType;

class Ticket extends Model
{
    use HasFactory;

    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'category_id',
        'user_id',
        'assigned_to',
        'file',
        'alerta'
    ];

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function supportReport()
    {
        return $this->hasOne(SupportReport::class);
    }

    // Agrega este accessor para el tiempo transcurrido
    public function getElapsedTimeAttribute()
    {
        return $this->created_at->locale('es')->diffForHumans();
    }

    // Y este para la fecha formateada
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function getFileUrlAttribute()
    {
        return $this->file ? Storage::url($this->file) : null;
    }

    public function getFileNameAttribute()
    {
        return $this->file ? basename($this->file) : null;
    }

    protected static function booted()
    {
        static::deleting(function ($ticket) {
            if ($ticket->file) {
                Storage::disk('public')->delete($ticket->file);
            }
        });
    }

}