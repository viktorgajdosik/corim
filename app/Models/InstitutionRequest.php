<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionRequest extends Model
{
    protected $fillable = [
        'name','org_domain','website_url','contact_email','message','status','decided_at','decided_by'
    ];

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
