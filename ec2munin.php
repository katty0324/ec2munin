<?php

require_once (dirname(__FILE__) . '/aws-sdk-for-php/sdk.class.php');

class Ec2munin {

	public static function start() {

		$configs = array();
		foreach (Ec2muninConfig::get_accounts() as $project => $option)
			$configs += self::create_configs($project, $option);

		$config = implode("\n\n", $configs);
		file_put_contents(Ec2muninConfig::get_config_path(), $config);

	}

	private static function create_configs($project, $option) {

		$ec2 = new AmazonEC2($option);

		$configs = array();
		foreach (Ec2muninConfig::get_regions() as $region) {

			$ec2->set_region($region);
			$instances = $ec2->describe_instances();
			if (!$instances->isOK())
				continue;

			foreach ($instances->body->reservationSet->children() as $reservationItem) {
				foreach ($reservationItem->instancesSet->children() as $instance) {
					$variables = self::extractVariables($instance);
					$variables['projectName']=$project;
					$configs[] = self::render(EC2muninConfig::get_template(), $variables);
				}
			}

		}

		return $configs;

	}

	private static function extractVariables($instance) {

		$variables = array(
			'instanceId' => $instance->instanceId->to_string(),
			'imageId' => $instance->imageId->to_string(),
			'instanceState' => $instance->instanceState->name->to_string(),
			'privateDnsName' => $instance->privateDnsName->to_string(),
			'dnsName' => $instance->dnsName->to_string(),
			'keyName' => $instance->keyName->to_string(),
			'instanceType' => $instance->instanceType->to_string(),
			'launchTime' => $instance->launchTime->to_string(),
			'availabilityZone' => $instance->placement->availabilityZone->to_string(),
			'kernelId' => $instance->kernelId->to_string(),
			'subnetId' => $instance->subnetId->to_string(),
			'vpcId' => $instance->vpcId->to_string(),
			'privateIpAddress' => $instance->privateIpAddress->to_string(),
			'ipAddress' => $instance->ipAddress->to_string(),
		);

		foreach ($instance->tagSet->item as $tag)
			$variables['tag.' . $tag->key->to_string()] = $tag->value->to_string();

		return $variables;

	}


	private function render($template, $variables) {

		foreach ($variables as $key => $value)
			$template = str_replace('${' . $key . '}', $value, $template);

		return $template;

	}

}
