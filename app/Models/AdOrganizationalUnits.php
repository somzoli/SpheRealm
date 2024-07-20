<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;
use LdapRecord\Models\ActiveDirectory\OrganizationalUnit;

class AdOrganizationalUnits extends Model
{
    use \Sushi\Sushi;

    public static array $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'user',
    ];

    protected $schema = [
        'name' => 'string',
        'distinguishedname' => 'string',
        'description' => 'string'
    ];

    public function getRows()
    {
        $setting = env('LDAP_BASE_DN');
        $ous = OrganizationalUnit::in($setting->value)->get()->sortBy('name');
        foreach ($ous as $ou) {
            $oudata[] = [
                'name' => $ou->getFirstAttribute('name'),
                'distinguishedname' => $ou->getFirstAttribute('distinguishedname'),
                'description' => $ou->getFirstAttribute('description')
            ];
        }
        return !empty($oudata) ? $oudata : [];
    }
}
