<?php

namespace App;
use App\Models;
use Exception;
use LdapRecord\Models\ActiveDirectory\Group;

class Controller
{
    public static function importUsers()
	{
		$admins = Models\Settings::where('option', 'ldap_admin_users')->first();
		(empty($admins->value)) ? throw new Exception('Ldap admin value missing!') : null;
		$members = (! empty(Group::find($admins->value))) ? Group::find($admins->value)->members()->get() : throw new Exception('Ldap admin value wrong!');
		$adminusers =  Models\User::get();
		// Collect datas
		foreach ($members as $member) {
			$objectguid = (! empty($member->getConvertedGuid())) ? $member->getConvertedGuid() : null;
			$guids[] = $objectguid;
		}
		// Delete if not exist anymore
		if (! empty($guids)) {
			foreach ($adminusers as $adminuser) {
				(! in_array($adminuser->guid, $guids) && $adminuser->guid) ? Models\User::where('guid', $adminuser->guid)->delete() : null;
			}
		}
		// Store
		foreach ($members as $member) {
			$realname = (empty($member->displayname[0])) ? $member->name[0] : $member->displayname[0];
			$email = (! empty($member->mail[0])) ? $member->mail[0] : null;
			$objectguid = (! empty($member->getConvertedGuid())) ? $member->getConvertedGuid() : null;
			$samaccountname = $member->samaccountname[0];
			Models\User::updateOrCreate(
				['guid' => $objectguid],
				[
					'name' => $samaccountname,
					'realname' => $realname,
					'email' => $email,
					'domain' => env('LDAP_CONNECTION', 'LDAP'),
					'guid' => $objectguid,
				]
			);
		}
	}
}