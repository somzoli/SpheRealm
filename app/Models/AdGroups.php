<?php

namespace App\Models;
use App\Models;
use LdapRecord\Models\ActiveDirectory\Group;
use LdapRecord\Models\ActiveDirectory\User;

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
		$setting = env('LDAP_BASE_DN');
        $groups = Group::in($setting)->get()->sortBy('name');
        foreach ($groups as $group) {
            $groupdata[] = [
                'name' => $group->getFirstAttribute('name'),
                'mail' => $group->getFirstAttribute('mail'),
                'description' => $group->getFirstAttribute('description'),
                'proxyaddresses' => $group->getFirstAttribute('proxyaddresses'),
                'distinguishedname' => $group->getFirstAttribute('distinguishedname'),
                // More data for detailed view
                'whencreated' => $group->getFirstAttribute('whencreated'),
                'whenchanged' => $group->getFirstAttribute('whenchanged'),
                'member' => !empty($group->member) ? nl2br(implode(",\n",$group->member)) : null,
                'gidnumber' => $group->getFirstAttribute('gidnumber'),
                'iscriticalsystemobject' => $group->getFirstAttribute('iscriticalsystemobject'),
                'objectcategory' => $group->getFirstAttribute('objectcategory'),
                'objectclass' => !empty($group->objectclass) ? nl2br(implode(",\n",$group->objectclass)) : null,
                'samaccountname' => $group->getFirstAttribute('samaccountname'),
            ];
        }
        return !empty($groupdata) ? $groupdata : [];
    }

    public static function allGroups()
    {
        $setting = env('LDAP_BASE_DN');
        $until = new \DateTime('+2 hours');
        $groups = Group::in($setting)->cache($until)->get()->sortBy('name');
        foreach ($groups as $group) {
            $result[] = 
                $group->getFirstAttribute('distinguishedname');
        }
        return !empty($result) ? $result : [];
    }

    public function members(): HasMany
    {
        return $this->hasMany([
            Group::class, User::class, Contact::class
        ], 'memberof')->using($this, 'member');
    }
}
