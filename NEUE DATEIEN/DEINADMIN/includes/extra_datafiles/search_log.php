<?php
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
define('TABLE_SEARCH_LOG', DB_PREFIX . 'search_log');
define('FILENAME_STATS_SEARCH_LOG', 'stats_search_log.php');
define('STATS_SEARCH_LOG_VERSION', '2.6.0');