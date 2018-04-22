<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public function attachments(){
        return $this->hasMany(Attachment::class);
    }

    public function firstAttachment(){
        return $this->hasOne(Attachment::class);
    }
}
