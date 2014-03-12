<?php

class Ec2muninConfig {

	private static $config_path;
	private static $regions;
	private static $accounts;
	private static $template;

	public static function get_config_path() {
		return self::$config_path;
	}

	public static function set_config_path($config_path) {
		self::$config_path = $config_path;
	}

	public static function get_regions() {
		return self::$regions;
	}

	public static function set_regions($regions) {
		self::$regions = $regions;
	}

	public static function get_accounts() {
		return self::$accounts;
	}

	public static function set_accounts($accounts) {
		self::$accounts = $accounts;
	}

	public static function get_template() {
		return self::$template;
	}

	public static function set_template($template) {
		self::$template = $template;
	}

}
