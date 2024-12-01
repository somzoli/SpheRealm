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
		(empty($admins->value)) ? throw new Exception('Ldap Admin Group DN missing!') : null;
		$members = (! empty(Group::find($admins->value))) ? Group::find($admins->value)->members()->get() : throw new Exception('Ldap Admin Group DN Wrong!');
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

	public static function importServers()
	{
		// Set php limits for large datas
		ini_set('memory_limit', '256M');
		ini_set('max_execution_time', '300');
		$servers = Models\Settings::where('option', 'ldap_servers')->first();
		(empty($servers->value)) ? throw new Exception('Ldap server value missing!') : null;
		$members = (! empty(Group::find($servers->value))) ? Group::find($servers->value)->members()->get() : throw new Exception('Ldap server value wrong!');
		$servers =  Models\Client::get();
		// Store & associate ip
		foreach ($members as $member) {
			$realname = (empty($member->displayname[0])) ? $member->name[0] : $member->displayname[0];
			$ip = gethostbyname(preg_replace('/[^a-zA-Z0-9_-]/', '', $realname));
			$server = Models\Client::updateOrCreate(
				[
					'name' => $realname,
					'ip' => '$ip',
					
				]
			);
		}
	}
}