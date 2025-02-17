<?php

namespace App;
use App\Models;
use Exception;
use LdapRecord\Models\ActiveDirectory\Group;
use Spatie\Dns\Dns;

class Controller
{
    public static function importUsers()
	{
		$admins = Models\Settings::where('option', 'ldap_admin_users')->first();
		(empty($admins->value)) ? throw new Exception('Ldap Admin Group DN missing!') : null;
		$members = (! empty(Group::find($admins->value))) ? Group::find($admins->value)->members()->recursive()->get() : throw new Exception('Ldap Admin Group DN Wrong!');
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
			if (!empty($member->objectclass) && !in_array('user', $member->objectclass)) {
				continue;
			}
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
		$dns = Models\Settings::where('option', 'dns_server')->first();
		(empty($servers->value)) ? throw new Exception('Ldap server option missing!') : null;
		(empty($dns->value)) ? throw new Exception('DNS server option missing!') : null;
		$members = (! empty(Group::find($servers->value))) ? Group::find($servers->value)->members()->get() : throw new Exception('Ldap server option wrong!');
		$servers =  Models\Client::get();
		// Store & associate ip
		foreach ($members as $member) {
			$name = (!empty($member->name[0])) ? $member->name[0] : 'Unknown';
			$full_dns = (!empty($member->dnshostname[0])) ? $member->dnshostname[0] : 'Unknown';
			$type_def = (!empty($member->operatingsystem[0])) ? strtolower($member->operatingsystem[0]) : 'Unknown';
			$description = (!empty($member->distinguishedname[0])) ? $member->distinguishedname[0] : 'Unknown';		
			$ip = (new Dns)->useNameserver($dns->value)->getRecords($full_dns, 'A')[0]->ip();
			if (str_contains($type_def, 'windows')) {
				$type = 'windows';
				$port = '3389';
			} elseif (str_contains($type_def, 'linux')) {
				$type = 'linux';
				$port = '22';
			} else {
				$type = 'Unknown';
				$port = 'Unknown';
			}
			$server = Models\Client::updateOrCreate(
				[
					'name' => $name,
					'ip' => $ip,
					'type' => $type,
					'description' => $description,
					//'port' => $port,
				],
				[
					'name' => $name,
					'ip' => $ip,
					'type' => $type,
					'description' => $description,
					//'port' => $port,
				]
			);
		}
	}
}