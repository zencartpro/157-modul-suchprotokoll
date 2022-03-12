CREATE TABLE IF NOT EXISTS search_log (
  search_log_id int NOT NULL auto_increment,
  search_term varchar(255),
  search_time datetime,
  search_results int,
  PRIMARY KEY  (search_log_id)
) ENGINE=MyISAM;

SELECT @max_sort_order_reports:=max(sort_order) FROM admin_pages WHERE menu_key = 'reports';
INSERT IGNORE INTO admin_pages (page_key, language_key, main_page, page_params, menu_key, display_on_menu, sort_order) VALUES 
('ReportsSearchLog', 'BOX_REPORTS_SEARCH_LOG', 'FILENAME_STATS_SEARCH_LOG', '', 'reports', 'Y', @max_sort_order_reports+1);