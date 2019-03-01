<?php defined('In33hao') or exit('Access Invalid!');?>
  <form method="get" id="formSearch">
    <table class="search-form">
      <input type="hidden" id='act' name='act' value='store_bill' />
      <input type="hidden" id='op' name='op' value='show_bill' />
      <input type="hidden" name='ob_id' value='<?php echo $_GET['ob_id'];?>' />
      <input type="hidden" name='type' value='<?php echo $_GET['type'];?>' />
      <tr>
        <td>&nbsp;</td>
        <th>订单编号</th>
        <td class="w180"><input type="text" class="text"  value="<?php echo $_GET['query_order_no'];?>" name="query_order_no" /></td>
        <td class="tc w200">
        <label class="submit-border"><input type="button" id="ncsubmit" class="submit" value="<?php echo $lang['nc_search'];?>" /></label>
        <label class="submit-border"><input type="button" id="ncexport" class="submit" value="导出" /></label>
        </td>
    </table>
  </form>
<table class="ncsc-default-table">
    <thead>
      <tr>
        <th class="w10"></th>
        <th>订单编号</th>
        <th>下单时间</th>
        <th>取消时间</th>
        <th>订单金额</th>
        <th>运费</th>
        <th>未退定金金额</th>
        <th><?php echo $lang['nc_handle'];?></th>
      </tr>
    </thead>
    <tbody>
      <?php if (is_array($output['order_list']) && !empty($output['order_list'])) { ?>
      <?php foreach($output['order_list'] as $order_info) { ?>
      <tr class="bd-line">
        <td></td>
        <td class="w90"><?php echo $order_info['order_sn'];?></td>
        <td><?php echo date("Y-m-d",$order_info['add_time']);?></td>
        <td><?php echo date("Y-m-d",$output['deposit_list'][$order_info['order_id']]['book_cancel_time']);?></td>
        <td><?php echo ncPriceFormat($order_info['order_amount']);?></td>
        <td><?php echo ncPriceFormat($order_info['shipping_fee']);?></td>
        <td><?php echo ncPriceFormat($output['deposit_list'][$order_info['order_id']]['book_real_pay']);?></td>
        <td>
       	<a target="_blank" href="index.php?act=store_order&op=show_order&order_id=<?php echo $order_info['order_id'];?>"><?php echo $lang['nc_view'];?></a>
        </td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="20" class="norecord"><i>&nbsp;</i><span><?php echo $lang['no_record'];?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php if (is_array($output['order_list']) && !empty($output['order_list'])) { ?>
      <tr>
        <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
<script type="text/javascript">
$(function(){
    $('#ncexport').click(function(){
    	$('input[name="op"]').val('export_book');
    	$('#formSearch').submit();
    });
    $('#ncsubmit').click(function(){
    	$('input[name="op"]').val('show_bill');
    	$('#formSearch').submit();
    });
});
</script>