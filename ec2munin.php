<?php

require_once (dirname(__FILE__) . '/aws-sdk-for-php/sdk.class.php');
require_once (dirname(__FILE__) . '/config.inc.php');

class Ec2munin {

	private $ec2;

	public function __construct() {
		$this->ec2 = new AmazonEC2();
	}

	public function start() {

		// get instance list and create config
		$config = "{$config_begin_seperater}\n";
		foreach ($regions as $region) {

			// describe instances in the region.
			$this->ec2->set_region($region);
			$instances = $this->ec2->describe_instances();
			if (!$instances->isOK())
				continue;

			// create config
			foreach ($instances->body->reservationSet->children() as $reservationItem) {
				foreach ($reservationItem->instancesSet->children() as $instanceItem) {
					$group_name = $region;
					$node_name = $instanceItem->dnsName;
					$node_ip = $use_public_dns ? $instanceItem->dnsName : $instanceItem->privateIpAddress;
					$config .= $this->create_munin_config($node_ip, $node_name, $group_name);
					continue;
				}
			}

		}
		$config .= "{$config_end_seperater}";

		// generate new config file
		$pattern = "/{$config_begin_seperater}(.*){$config_end_seperater}/s";
		$old_config = @file_get_contents($config_path);
		$new_config = preg_replace($pattern, $config, $old_config);
		if (!preg_match($pattern, $new_config))
			$new_config = "{$old_config}\n{$config}";

		file_put_contents($config_path, $new_config);

	}

	private function create_munin_config($node_ip, $node_name = null, $group_name = null) {
		if (!$node_ip)
			return null;
		if (!$node_name)
			$node_name = $node_ip;
		if (!$group_name)
			$group_name = 'muninec2';
		return "[{$group_name};{$node_name}]\n\taddress	{$node_ip}\n\tuse_node_name	yes\n\n";
	}

}
