<?php

require_once (dirname(__FILE__) . '/Ec2muninConfig.php');

Ec2muninConfig::set_config_path('/etc/munin/conf.d/ec2munin.conf');

Ec2muninConfig::set_regions(array(
	AmazonEC2::REGION_US_E1,
	AmazonEC2::REGION_US_W1,
	AmazonEC2::REGION_US_W2,
	AmazonEC2::REGION_EU_W1,
	AmazonEC2::REGION_APAC_SE1,
	AmazonEC2::REGION_APAC_NE1,
	AmazonEC2::REGION_US_GOV1,
	AmazonEC2::REGION_SA_E1,
));

Ec2muninConfig::set_accounts(array('project' => array(
		'key' => 'key',
		'secret' => 'secret-key',
	), ));

Ec2muninConfig::set_template("[\${projectName};\${tag.Name};\${dnsName}]\n\taddress\t\${dnsName}\n\tuse_node_name\tyes");
