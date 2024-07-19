<?php

namespace App\Models;
use App\Models;
use LdapRecord\Models\ActiveDirectory\Group;

//use LdapRecord\Models\Model;
use Illuminate\Database\Eloquent\Model;

class AdGroups extends Model
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
        'mail' => 'string',
        'proxyaddresses' => 'string',
        'description' => 'string',
        'distinguishedname' => 'string'
    ];

    public function getRows()
    {
        ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', '3000');
		$setting = Models\Settings::where('option', 'ldap_base')->first('value');
        $groups = Group::in($setting->value)->get()->sortBy('name');
        foreach ($groups as $group) {
            $groupdata[] = [
                'name' => $group->getFirstAttribute('name'),
                'mail' => $group->getFirstAttribute('mail'),
                'description' => $group->getFirstAttribute('description'),
                'proxyaddresses' => $group->getFirstAttribute('proxyaddresses'),
                'distinguishedname' => $group->getFirstAttribute('distinguishedname'),
                // More data for detailed view
                'whencreated' => $group->getFirstAttribute('whencreated'),
                'member' => !empty($group->member) ? nl2br(implode(",\n",$group->member)) : null,
                'gidnumber' => $group->getFirstAttribute('gidnumber'),

            ];
        }
        return !empty($groupdata) ? $groupdata : [];
    }
}
