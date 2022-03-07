<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Events\CompanyActivity;


class ContactsImport implements ToModel,WithHeadingRow
{

    private $data;

    public function __construct(array $data = [])
    {
        $this->data = $data; 
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $contact = [
            'name' => $row['name'],
            'user_id' => $this->data['user_id'], 
            'phone_number' => $row['phone_number'], 
            'company_id' => $this->data['company_id'],
        ];
        return new Contact($contact);
    }
}
