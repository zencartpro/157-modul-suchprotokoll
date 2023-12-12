<?php
/**
* Zen Cart German Specific
* Written By C.J.Pinder (c) 2007
* @copyright Copyright 2003-2022 Zen Cart Development Team
* Zen Cart German Version - www.zen-cart-pro.at
* @copyright Portions Copyright 2003 osCommerce
* @license https://www.zen-cart-pro.at/license/3_0.txt GNU General Public License V3.0
* @version $Id: stats_search_log.php 2023-12-12 18:12:16Z webchills $
*/
require('includes/application_top.php');
   

   function export_search_log()
   {
     global $db;
   $file = urlencode('searchlog') . ".csv";      
   	  if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT']))
   	  {
   		header('Content-Type: application/octetstream');
   		header('Content-Disposition: attachment; filename=' . $file);
   		header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
   		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
   		header("Cache-Control: must_revalidate, post-check=0, pre-check=0");
   		header("Pragma: public");
   		header("Cache-control: private");
   	  } else
   	  {
   		header('Content-Type: application/x-octet-stream');
   //              header('Content-Type: '.$content_type);
   		header('Content-Disposition: attachment; filename=' . $file);
   		header("Expires: Mon, 26 Jul 2001 05:00:00 GMT");
   		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
   		header("Pragma: no-cache");
   	  }
   $sql = "select search_time, search_term, search_results from " . TABLE_SEARCH_LOG . " order by search_time desc";
   $result = $db->Execute($sql);
   
   echo '"'.TABLE_HEADING_DATE . '","' . TABLE_HEADING_SEARCH_TERM . '","' . TABLE_HEADING_RESULTS . '"' . "\n";
   
    while(!$result->EOF)
    {
     echo $result->fields['search_time'].',';
     $phrase = str_replace('"', '""', $result->fields['search_term']);
     echo '"' . $phrase . '",';
     echo $result->fields['search_results']."\n";
     $result->MoveNext();
    }
   }
   
   if (!isset($_GET['list_order'])) $_GET['list_order'] = '';
   
   $action = (isset($_GET['action']) ? $_GET['action'] : '');
   
   switch($action)
   {
   case 'clear_search_log':
     $db->Execute("DELETE FROM ".TABLE_SEARCH_LOG." WHERE search_time < DATE_SUB(curdate(), INTERVAL ".(int)$_POST['days']." DAY)");
     $db->Execute("optimize table " . TABLE_SEARCH_LOG);
     $messageStack->add_session(SUCCESS_CLEAN_SEARCH_LOG, 'success');
     zen_redirect(zen_href_link(FILENAME_STATS_SEARCH_LOG));
     break;
   
   case 'export_search_log':
     export_search_log();
     break;
   }
   
   if ($action != 'export_search_log'){
   ?>
<!DOCTYPE html>
<!--doctype changed to stop quirks mode -->
<html <?php echo HTML_PARAMS; ?>>
   <head>
   <?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
       
   </head>
   <body >
      <!-- header //-->
      <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
      <!-- header_eof //--> 
      <!-- body //-->
       <div class="container-fluid">
     
            <!-- body_text //-->
             
                <h1><?php echo HEADING_TITLE.' '.STATS_SEARCH_LOG_VERSION; ?> </h1>
                
                    
                <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-4">         
                          <p>       <?php echo zen_draw_form('clear_search_log', FILENAME_STATS_SEARCH_LOG, 'action=clear_search_log','post');  ?></p>
                                 
                                     <p><?php printf (TEXT_DELETE_OLD_RECORDS, zen_draw_input_field('days','7','size="4"')); ?></p>
                                     <p><?php echo zen_image_submit('button_delete.gif', IMAGE_DELETE); ?></p>
                                   
                                 </form>
   </div>               
                <div class="col-lg-4 col-md-4 col-sm-4 col-4">        
                                <p> <?php echo zen_draw_form('export_search_log', FILENAME_STATS_SEARCH_LOG, 'action=export_search_log','post'); ?></p>
                                 
                                    
                                       <p><?php echo TEXT_EXPORT_SEARCH_LOG; ?></p>
                                      <p><?php echo zen_image_submit('button_save.gif', IMAGE_SAVE); ?></p>
                                
                                 </form>
   </div>                
                </div>
 
 
                        <table class="table table-striped  ">
                           
                           <tr>
                              <th>                      
                                      <?php  echo TABLE_HEADING_DATE; ?>  
                                    </th>
                                     <th>                                    
                                        <?php   echo TABLE_HEADING_SEARCH_TERM; ?>   
                                    </th>
                                     <th>                                      
                                       <?php    echo  TABLE_HEADING_RESULTS; ?>  
                                        </th>
                                   </tr>
                           
                                    <?php                                   
                                  
                                 $list_order = isset($_GET['list_order']) ? $_GET['list_order']:'';

                                       switch ($list_order)
                                       {
                                         case "searchdate":
                                       	  $disp_order = "search_time";
                                       	  break;
                                         case "searchdate-desc":
                                       	  $disp_order = "search_time DESC";
                                       	  break;
                                         case "searchterm":
                                       	  $disp_order = "search_term, search_time DESC";
                                       	  break;
                                         case "searchterm-desc":
                                       	  $disp_order = "search_term DESC, search_time DESC";
                                       	  break;
                                         case "searchresults":
                                       	  $disp_order = "search_results, search_time DESC";
                                       	  break;
                                         case "searchresults-desc":
                                       	  $disp_order = "search_results DESC, search_time DESC";
                                       	  break;
                                         default:
                                       	  $disp_order = "search_time DESC";
                                       	  break;
                                       }
                                       
                                       $search_query_raw = "select search_results, search_term, search_time from " . TABLE_SEARCH_LOG . " order by " . $disp_order;
                                       $search_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $search_query_raw, $search_query_numrows);
                                       $search_terms = $db->Execute($search_query_raw);
                                       while (!$search_terms->EOF)
                                       {
                                       ?>
                                       
                                    <tr >
                                       <td  ><?php echo $search_terms->fields['search_time']; ?>&nbsp;&nbsp;</td>
                                       <td ><?php echo htmlspecialchars(stripslashes($search_terms->fields['search_term'])); ?>&nbsp;&nbsp;</td>
                                       <td ><?php echo $search_terms->fields['search_results']; ?></td>
                                    </tr>
                                    
                                    
                                        
                                  
                                 <?php
                                       $search_terms->MoveNext();
                                       }
                                       ?>
                                    <tr >
                                       <td ><?php echo $search_split->display_count($search_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SEARCHES); ?></td>
                                       <td ><?php echo $search_split->display_links($search_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], zen_get_all_get_params(array('page'))); ?></td>
                                    <td>&nbsp;</td>
                           </tr>
                                       
                                 </table>
                                
                     
                  
               
            
            <!-- body_text_eof //--> 
         
      
      <!-- body_eof //--> 
      </div>
      <!-- footer //-->
      <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
      <!-- footer_eof //-->
   </body>
</html>
<?php } ?>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>