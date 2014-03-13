# EC2Munin

Munin config file generator for Amazon EC2

## How to use

Install from GitHub.

```bash
git clone https://github.com/katty0324/ec2munin.git
cd ec2munin/
git submodule init
git submodule update
```

Add AWS access key and secret to config.php

```php
Ec2muninConfig::set_accounts(array('project' => array(
		'key' => 'key',
		'secret' => 'secret-key',
	), ));
```

Run EC2Munin and configuration file will be generated.

```bash
./ec2munin
```

If you want to run EC2Munin every 5 minutes, edit cron config.

```
*/5 * * * * /home/username/ec2munin/ec2munin > /dev/null 2>&1
```

## Configuration

You can change the path of auto generated file.

```php
Ec2muninConfig::set_config_path('/etc/munin/conf.d/ec2munin.conf');
```

Multiple AWS accounts are allowed.

```php
Ec2muninConfig::set_accounts(array(
    'probject-1' => array(
        'key' => 'key-1',
        'secret' => 'secret-1',
    ),
    'project-2' => array(
        'key' => 'key-2',
        'secret' => 'secret-2',
    ),
    'project-3' => array(
        'key' => 'key-3',
        'secret' => 'secret-3',
    ),
));

```

## Template

Configuration line is set in following line. 

```php
Ec2muninConfig::set_template("[\${projectName};\${tag.Name};\${dnsName}]\n\taddress\t\${dnsName}\n\tuse_node_name\tyes");
```

This template will be converted into the config below.

```
[project-1;mysql-server;ec2-xx-xx-xx-xx.ap-northeast-1.compute.amazonaws.com]
	address	ec2-xx-xx-xx-xx.ap-northeast-1.compute.amazonaws.com
	use_node_name	yes
```

${xxx} in template will be replaced. You can use folowing variables. 

```
instanceId
imageId
instanceState
privateDnsName
dnsName
keyName
instanceType
launchTime
availabilityZone
kernelId
subnetId
vpcId
privateIpAddress
ipAddress
tag.XXX (XXX is EC2 instance tag)
projectName (key of one of the accounts set in Ec2muninConfig)
```

# License

EC2Munin is unser MIT license.
