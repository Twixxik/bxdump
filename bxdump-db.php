<?php

define('NO_AGENT_CHECK', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('NOT_CHECK_PERMISSIONS', true);

$_SERVER['DOCUMENT_ROOT'] = getcwd();
$_SERVER['SCRIPT_NAME'] = $_SERVER['DOCUMENT_ROOT'] . '/index.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Config\Configuration;

$connection = Configuration::getValue('connections')['default'];

[
	'host' => $host,
	'database' => $database,
	'login' => $login,
	'password' => $password,
] = $connection;

if (strpos($host, ':') !== false) {
	[$host, $port] = explode(':', $host);
}

if (!$port) {
	$port = 3306;
}

$vars = implode(PHP_EOL, [
	'set host=' . '"' . $host . '"',
	'set port=' . '"' . $port . '"',
	'set login=' . '"' . $login . '"',
	'set password=' . '"' . $password . '"',
	'set database=' . '"' . $database . '"',
]);

$mysqldump = 'mysqldump -h$host -P$port -u$login -p$password $database'

$commands = [
	$vars,
	$mysqldump . ' --no-tablespaces --no-data --routines --events > ' . __DIR__ . '/10-structure.sql',
	implode(' ', [
		$mysqldump,
		'--no-tablespaces',
		'--no-create-info',
		'--ignore-table=' . $database . '.b_event',
		'--ignore-table=' . $database . '.b_event_log',
		'--ignore-table=' . $database . '.b_im_message',
		'--ignore-table=' . $database . '.b_im_message_param',
		'--ignore-table=' . $database . '.b_im_message_favorite',
		'--ignore-table=' . $database . '.b_stat_hit',
		'--ignore-table=' . $database . '.b_stat_page',
		'--ignore-table=' . $database . '.b_stat_path',
		'--ignore-table=' . $database . '.b_stat_guest',
		'--ignore-table=' . $database . '.b_stat_session',
		'--ignore-table=' . $database . '.b_stat_path_cache',
		'--ignore-table=' . $database . '.b_stat_referer_list',
		'--ignore-table=' . $database . '.b_stat_searcher_hit',
		'--ignore-table=' . $database . '.b_perf_sql',
		'> ' . __DIR__ . '/20-data.sql',
	]),
	'tar -czf ' . __DIR__ . '/bxdump-db.tar.gz ' . __DIR__ . '/10-structure.sql ' . __DIR__ . '/20-data.sql'
];

foreach ($commands as $command) {
	$output = system($command, $code);
	echo $output . PHP_EOL;
	if ($code !== 0) {
		return;
	}
}