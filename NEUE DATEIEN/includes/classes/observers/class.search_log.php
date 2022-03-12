<?php
// Search Log v2.6.0
// Written By C.J.Pinder (c) 2007
// Portions Copyright 2003-2021 Zen Cart Development Team
// Portions Copyright 2003 osCommerce
//
// This source file is subject to version 2.0 of the GPL license, 
// that is bundled with this package in the file LICENSE, and is
// available through the world-wide-web at the following url:
// http://www.zen-cart.com/license/2_0.txt
// If you did not receive a copy of the zen-cart license and are unable
// to obtain it through the world-wide-web, please send a note to
// license@zen-cart.com so we can mail you a copy immediately.    

if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

class search_log extends base
{
	function __construct()
	{
		global $zco_notifier, $session_started;
		if ($session_started)
			$zco_notifier->attach($this, array('NOTIFY_SEARCH_ORDERBY_STRING'));
	}

	function update(&$class, $eventID, $param1)
	{
		global $db;
		global $from_str, $where_str, $order_str;
      $exclude_admin = true; 

      if ($exclude_admin) { 
         if (!empty($_SERVER['REMOTE_ADDR']) && !empty(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE)) {
            if (strpos(EXCLUDE_ADMIN_IP_FOR_MAINTENANCE, $_SERVER['REMOTE_ADDR']) !== false) { 
               return; 
            }
         }
      }

		switch ($eventID)
		{
			case 'NOTIFY_SEARCH_ORDERBY_STRING':
				$search_term = trim($_GET['keyword']);
				if (!isset($_SESSION['search_log_term']) || ($_SESSION['search_log_term'] != $search_term))
				{
					$_SESSION['search_log_term'] = $search_term;

					// when the NOTIFY_SEARCH_ORDERBY_STRING notifier fires, it passes $listing_sql as $param1, so, we'll use that to check how many results are found
					if (!empty($param1) && is_string($param1)) {
						$listing_sql = $param1;
						$result = new splitPageResults($listing_sql, 10000, 'p.products_id', 'page');
						$num_results = $result->number_of_rows;
					} else {
						// fallback for older versions .... BUT note that this may trigger SQL strict errors about missing GROUP BY clauses
						$search_count_query = 'select count(distinct p.products_id) as rescount ' . $from_str . $where_str . $order_str;
						$search_count = $db->Execute($search_count_query);
						$num_results = $search_count->fields['rescount'];
					}

					$sql = 'insert into ' .DB_PREFIX. 'search_log (search_term, search_time, search_results) values (:searchTerm,now(),:searchResults)';
					$sql = $db->bindVars ($sql, ':searchTerm', $search_term, 'string');
					$sql = $db->bindVars ($sql, ':searchResults', $num_results, 'integer');
					$db->Execute($sql);
				}
				break;
		}
	}
}
