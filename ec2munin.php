<?php

require_once (dirname(__FILE__) . '/aws-sdk-for-php/sdk.class.php');

class Ec2munin {

	public static function start() {

		$config = '';
		foreach (Ec2muninConfig::get_accounts() as $project => $config)
			$config .= self::create_configs($project, $config);

		file_put_contents(Ec2muninConfig::get_config_path(), $config);

	}

	private static function create_configs($project, $config) {

		$ec2 = new AmazonEC2($config);

		// get instance list and create config
		$config = '';
		foreach (Ec2muninConfig::get_regions() as $region) {

			// describe instances in the region.
			$ec2->set_region($region);
			$instances = $ec2->describe_instances();
			if (!$instances->isOK())
				continue;

			// create config
			foreach ($instances->body->reservationSet->children() as $reservationItem) {
				foreach ($reservationItem->instancesSet->children() as $instanceItem) {
					$group_name = $region;
					$node_name = $instanceItem->dnsName;
					$node_ip = $instanceItem->dnsName;
					$config .= self::create_munin_config($node_ip, $node_name, $group_name);
					continue;
				}
			}

		}

		return $config;

	}

	private static function create_munin_config($node_ip, $node_name = null, $group_name = null) {
		if (!$node_ip)
			return null;
		if (!$node_name)
			$node_name = $node_ip;
		if (!$group_name)
			$group_name = 'muninec2';
		return "[{$group_name};{$node_name}]\n\taddress	{$node_ip}\n\tuse_node_name	yes\n\n";
	}

}
