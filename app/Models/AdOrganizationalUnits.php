<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models;
use LdapRecord\Models\ActiveDirectory\OrganizationalUnit;
use Exception;

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
        $ous = OrganizationalUnit::in($setting)->recursive()->get()->sortBy('name');
        foreach ($ous as $ou) {
            $oudata[] = [
                'name' => $ou->getFirstAttribute('name'),
                'distinguishedname' => $ou->getFirstAttribute('distinguishedname'),
                'description' => $ou->getFirstAttribute('description'),
                'whencreated' => $ou->getFirstAttribute('whencreated'),
                'whenchanged' => $ou->getFirstAttribute('whenchanged'),
                'showinadvancedviewonly' => $ou->getFirstAttribute('showinadvancedviewonly'),
                'objectcategory' => $ou->getFirstAttribute('objectcategory'),
                'iscriticalsystemobject' => $ou->getFirstAttribute('iscriticalsystemobject'),
                'gplink' => $ou->getFirstAttribute('gplink'),
            ];
        }
        return !empty($oudata) ? $oudata : [];
    }
    public static function allOus()
    {
        $setting = env('LDAP_BASE_DN');
        $until = new \DateTime('+2 hours');
        $ous = OrganizationalUnit::in($setting)->cache($until)->recursive()->get()->sortBy('name');
        foreach ($ous as $ou) {
            $result[$ou->getFirstAttribute('distinguishedname')] = 
                $ou->getFirstAttribute('distinguishedname');
        }
        return !empty($result) ? $result : [];
    }

    public static function createOu($data)
    {
        OrganizationalUnit::where('ou', '=', $data['name'])->exists() ? throw new Exception('OU exists.') : null;
        !empty($data['organizational_unit']) ? $setting =  $data['organizational_unit'] : $setting = env('LDAP_BASE_DN');
        $ou = (new OrganizationalUnit)->inside($setting);
        $ou->ou = $data['name'];
        $ou->description = $data['description'];
        try {
            $ou->save();
        } catch (\LdapRecord\LdapRecordException $e) {
            return 'Failed to create OU.'.$e;
        }
    }
}
