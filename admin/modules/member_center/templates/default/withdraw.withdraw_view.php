<?php defined('In33hao') or exit('Access Invild!');?>
<div class="ncap-form-default">
	<dl class="row">
      	<dt class="tit">
        	<label>提现编号</label>
      	</dt>
      	<dd class="opt"><?php echo $output['w_info'][0]['wl_sn']; ?>
        	<p class="notic"></p>
      	</dd>
    </dl>
    <dl class="row">
      	<dt class="tit">
        	<label>会员名</label>
      	</dt>
      	<dd class="opt"><?php echo $output['n_info'][$output['w_info'][0]['wl_member_id']]; ?>
        	<p class="notic"></p>
      	</dd>
    </dl>
    <dl class="row">
      	<dt class="tit">
        	<label>提现金额</label>
      	</dt>
      	<dd class="opt"><?php echo $output['w_info'][0]['wl_amount']; ?>&nbsp;元
        	<p class="notic"></p>
      	</dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label>手续费</label>
      </dt>
      <dd class="opt"><?php echo $output['w_info'][0]['wl_fee']; ?>&nbsp;元
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label>到账金额</label>
      </dt>
      <dd class="opt"><?php echo floatval($output['w_info'][0]['wl_amount'] - $output['w_info'][0]['wl_fee']) ?>&nbsp;元
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      	<dt class="tit">
        	<label>开户行</label>
      	</dt>
      	<dd class="opt"><?php echo $output['w_info'][0]['wl_account_bank_name']; ?>
        	<p class="notic"></p>
      	</dd>
    </dl>
    <dl class="row">
        <dt class="tit">
            <label>开户支行</label>
        </dt>
        <dd class="opt"><?php echo $output['w_info'][0]['wl_account_branch']; ?>
            <p class="notic"></p>
        </dd>
    </dl>
    <dl class="row">
      	<dt class="tit">
        	<label>银行账号</label>
      	</dt>
      	<dd class="opt"><?php echo $output['w_info'][0]['wl_account_num']; ?>
        	<p class="notic"></p>
      	</dd>
    </dl>
    <dl class="row">
      	<dt class="tit">
        	<label>开户姓名</label>
      	</dt>
      	<dd class="opt"><?php echo $output['w_info'][0]['wl_account_name']; ?>
        	<p class="notic"></p>
      	</dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="">状态</label>
      </dt>
      <dd class="opt"><?php echo str_replace(array(1, 2), array('已支付', '已拒绝'), $output['w_info'][0]['wl_state']); ?>
        <p class="notic"></p>
      </dd>
    </dl>

  </div>