<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContactModel extends Model
{
    use HasFactory;

    protected $table = 'contacts'; // ✅ đổi từ tbl_contact

    public function getContacts()
    {
        return DB::table($this->table)
            ->where('isReply', 'n')
            ->orderBy('id', 'desc') // ✅ đổi từ contactId
            ->get();
    }

    public function updateContact($contactId, $data)
    {
        return DB::table($this->table)
            ->where('id', $contactId) // ✅ đổi từ contactId
            ->update($data);
    }

    public function countContactsUnread()
    {
        $contacts = DB::table($this->table)
            ->where('isReply', 'n')
            ->orderBy('id', 'desc') // ✅ đổi từ contactId
            ->get();

        return [
            'countUnread' => $contacts->count(),
            'contacts' => $contacts
        ];
    }
}