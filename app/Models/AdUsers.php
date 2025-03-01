<?php

namespace App\Models;
use App\Models;

//use LdapRecord\Models\Model;
use Illuminate\Database\Eloquent\Model;
use LdapRecord\Models\ActiveDirectory\User;
use LdapRecord\Models\ActiveDirectory\Group;
use LdapRecord\Models\Attributes\AccountControl;
use Illuminate\Support\Facades\Storage;
use LdapRecord\Models\Attributes\Timestamp;
use Exception;

class AdUsers extends Model
{
    use \Sushi\Sushi;

    public static array $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'user',
    ];

    /*protected $schema = [
        'active' => 'string',
        'name' => 'string',
        'samaccountname' => 'string',
        'mail' => 'string',
        'description' => 'string',
        'otherMailbox' => 'string',
        'distinguishedname' => 'string',
        'jpegphoto' => 'string'
    ];*/

    public static function getLocked($userdn)
    {
        $user = User::find($userdn);
        if ($user->isEnabled()) {
            return 'True';
        } else {
            return 'False';
        }
    }

    public static function getExpire($user)
    {
        if ($user->accountExpires === false) {
            return 'No expire set';
        } else if (in_array($user->accountExpires, [0, Timestamp::WINDOWS_INT_MAX], $strict = true)) {
            return 'Never expire';
        } else if ($user->accountExpires->isPast()) {
            return 'Account expired';
        } else {
            return 'Account is not expired';
        }
    }

    public static function getGroups($user)
    {
        $groups = $user->groups()->get();
        foreach ($groups as $group) {
            $data[] =  $group->getName();
        }
        return (!empty($data)) ? implode(",",$data) : null;
    }

    public function getRows()
    {
        ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', '3000');
		$setting = env('LDAP_BASE_DN');
        $until = new \DateTime('+2 hours');
        $users = User::in($setting)->cache($until)->recursive()->get()->sortBy('name');
        foreach ($users as $user) {
            // Store avatar
            $avatar = (! empty($user->getFirstAttribute('jpegphoto'))) ? $user->getFirstAttribute('jpegphoto') : $user->getFirstAttribute('thumbnailPhoto');
            $avatar_exist = !empty($avatar) ? Storage::disk('public')->exists('storage/avatars/'.$user->getFirstAttribute('samaccountname').'.jpeg') : null;
            $avatar_local = $avatar_exist ? Storage::disk('public')->get('storage/avatars/'.$user->getFirstAttribute('samaccountname').'.jpeg') : null;
            $avatar != $avatar_local ? Storage::disk('public')->put('storage/avatars/'.$user->getFirstAttribute('samaccountname').'.jpeg', $avatar) : null;
            $imageurl = $avatar_exist ? Storage::disk('public')->url('storage/avatars/'.$user->getFirstAttribute('samaccountname').'.jpeg') : null;
            $data[] = [
                'active' => self::getLocked($user->getFirstAttribute('distinguishedname')),
                'name' => $user->getFirstAttribute('name'),
                'samaccountname' => $user->getFirstAttribute('samaccountname'),
                'mail' => $user->getFirstAttribute('mail'),
                'description' => $user->getFirstAttribute('description'),
                'otherMailbox' => $user->getFirstAttribute('otherMailbox'),
                'distinguishedname' => $user->getFirstAttribute('distinguishedname'),
                // More data for detail view
                'jpegphoto' => $imageurl,
                'memberof' => !empty($user->memberof) ? nl2br(implode(",\n",$user->memberof)) : null,
                'displayname' => $user->getFirstAttribute('displayname'),
                'lastlogon' => $user->getFirstAttribute('lastlogon'),
                'accountexpires' => self::getExpire($user),
                'userprincipalname' => $user->getFirstAttribute('userprincipalname'),
                'uidnumber' => $user->getFirstAttribute('uidnumber'),
                'whenchanged' => $user->getFirstAttribute('whenchanged'),
                'badpwdcount' => $user->getFirstAttribute('badpwdcount'),
                'loginshell' => $user->getFirstAttribute('loginshell'),
            ];
        }
        return !empty($data) ? $data : [];
    }

    public static function allUsers($data = null)
    {
        $setting = env('LDAP_BASE_DN');
        $until = new \DateTime('+2 hours');
        $users = User::in($setting)->cache($until)->recursive()->get()->sortBy('name');
        foreach ($users as $user) {
            $result[$user->getFirstAttribute('distinguishedname')] = 
                $user->getFirstAttribute('distinguishedname');
        }
        return !empty($result) ? $result : [];
    }

    public static function createUser($data)
    {
        User::where('samaccountname', '=', $data['username'])->exists() ? throw new Exception('User exists.') : null;
        !empty($data['organizational_unit']) ? $setting =  $data['organizational_unit'] : $setting = env('LDAP_BASE_DN');
        $user = (new User)->inside($setting);
        $user->cn = $data['name'];
        $user->unicodePwd = $data['password'];
        $user->samaccountname = $data['username'];
        $user->mail = $data['email'];
        $user->description = !empty($data['description']) ? $data['description'] : null;
        $user->save();
        // Sync the created users attributes.
        $user->refresh();
        // Enable the user.
        $user->userAccountControl = 512;
        try {
            $user->save();
            if (!empty($data['groups'])) {
                foreach ($data['groups'] as $group) {
                    $grp = Group::findOrFail($group);
                    $user->groups()->attach($grp);
                }
            }
        } catch (\LdapRecord\LdapRecordException $e) {
            return 'Failed to create User.'.$e;
        }
    }

    public static function resetPassword($admodel,$data) 
    {
        $user = User::find($admodel->getFirstAttribute('distinguishedname'));
        $user->unicodePwd = $data['password'];
        try {
            $user->save();
        } catch (\LdapRecord\Exceptions\InsufficientAccessException $ex) {
            // The currently bound LDAP user does nothave permission to reset passwords.
            return 'Failed resetting password'.$ex;
        } catch (\LdapRecord\Exceptions\ConstraintException $ex) {
            // The users new password does not abide by the domains password policy.
            return 'Failed resetting password'.$ex;
        } catch (\LdapRecord\LdapRecordException $ex) {
            // Failed resetting password. Get the last LDAP error to determine the cause of failure.
            return 'Failed resetting password'.$ex;
        }
        
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'member');
    }
}
