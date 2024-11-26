<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatsInbox extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'cc',
        'subject',
        'message',
        'ready',
        'status',
    ];


    public function getInbox($id)
    {
        $inbox = ChatsInbox::find($id);
        $data = [];

        $data['id']   = $inbox->id;
        $data['user'] = User::find($inbox->user_id);
        $data['subject']    = $inbox->subject;
        $data['message']    = $inbox->message;
        $data['ready']      = $inbox->ready;
        $data['status']     = $inbox->status;
        $data['init']       = $inbox->created_at->isoFormat('DD-MM-Y H:mm A');
        $data['created']    = $inbox->created_at->isoFormat('H:mm A');
        $data['replys']     = ChatsInbox::where('cc',$id)->OrderBy('id','DESC')->get();

        return $data;
    }

}
