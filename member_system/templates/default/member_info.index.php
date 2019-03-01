<?php defined('In33hao') or exit('Access Invalid!');?>
<div class="ncsc-index">
  <div class="top-container">
    <div class="basic-info">
      <dl class="ncsc-seller-info">
        <dt class="seller-name">
          <h3><?php echo $output['info']['member_mobile']; ?></h3>
<!--          --><?php //if ($output['info']['is_service']): ?>
<!--            <h5 style="padding: 7px; width: 50px; height: 10px; border-radius: 3px; background-color: #6f9e3b;">服务中心</h5>-->
<!--          --><?php //endif?>
        </dt>
        <dd>
            <img src="<?php echo getMemberAvatarForID($output['info']['member_id']); ?>" style="width: 75px;height:75px;border:2px solid;border-radius: 75px;margin-left: 62px"  />
        </dd>
        <dd class="seller-permission">姓名：<strong><?php echo $output['info']['member_name']; ?></strong></dd>
         <dd class="seller-last-login">会员等级：<strong><?php echo str_replace(array(0,1,2,3,4,5),array("体验用户","VIP","店主","合伙人","高级合伙人","战略合伙人"),$output['info']['member_level']); ?></strong> </dd><br>
        <dd class="store-name">最后登录：<strong><?php echo date('Y-m-d H:i:s', $output['info']['member_old_login_time']); ?></strong></dd>
      </dl>
    </div>
  </div>
  <div class="seller-cont">
    <div class="container type-a">
      <div class="hd">
        <h3>我的账户</h3>
      </div>
      <div class="content">
        <dl>
          <dt style="font-size: 15px;font-weight: 200">我的余额：</dt>
                    <dd style="width: 60px;text-align: center;">
            <em style="color: #008052;font-size: 16px;font-weight: 600"><?php echo Model('member')->getMemberBalanceById($output['info']['member_id']); ?></em>
          </dd>
          <dd style="margin-left: 15px">
              <span>
              <a href="index.php?act=member_property_operate&op=withdraw_apply" style="padding:3px 7px;border-radius:5px;background-color: #FF3300;">
                <em style="color: whitesmoke">提现</em>
              </a>
            </span>
          </dd>
<!--          <dd>-->
<!--            <span>-->
<!--              <a href="index.php?act=member_property_operate&op=bean_donate" style="padding:3px 7px;border-radius:5px;background-color: #0099FF;">-->
<!--                <em style="color: whitesmoke">转赠</em>-->
<!--              </a>-->
<!--            </span>-->
<!--          </dd>-->
<!--          <dd>-->
<!--             <span>-->
<!--              <a href="index.php?act=member_property_operate&op=consume_apply" style="padding:3px 7px;border-radius:5px;background-color: #f57402;">-->
<!--                <em style="color: whitesmoke">消费</em>-->
<!--              				</a>-->
<!--            			</span>-->
<!--                    </dd>-->
                </dl>
                <dl>
                    <dt style="font-size: 15px;font-weight: 200">我的奖励：</dt>
                    <dd style="width: 60px;text-align: center;">
                        <em style="color: #008052;font-size: 16px;font-weight: 600"><?php echo Model('member')->getMemberBounsById($output['info']['member_id']); ?></em>
                    </dd>
<!--                    <dd>-->
<!--                        <span>-->
<!--              				<a href="index.php?act=member_property_operate&op=silver_bean_donate" style="padding:3px 7px;border-radius:5px;background-color: #0099FF;">-->
<!--                				<em style="color: whitesmoke">转赠</em>-->
<!--              				</a>-->
<!--            			</span>-->
<!--                    </dd>-->
                </dl>
                <dl>
                    <dt style="font-size: 15px;font-weight: 200">我的库存：</dt>
                    <dd>
                        <em style="color: #008052;font-size: 16px;font-weight: 600"><?php echo Model('member')->getMemberInventoryById($output['info']['member_id']); ?></em>
                    </dd>
<!--                    <dt style="font-size: 15px;font-weight: 200;margin-left: 110px">消费额：</dt>-->
<!--                    <dd>-->
<!--                        <em style="color: #008052;font-size: 16px;font-weight: 600">--><?php //echo $output['consuem_amount']; ?><!--</em>-->
<!--                    </dd>-->
                </dl>
<!--                <dl>-->
<!--                    <dt style="font-size: 15px;font-weight: 200">金券数量：</dt>-->
<!--                    <dd>-->
<!--                        <em style="color: #008052;font-size: 16px;font-weight: 600">--><?php //echo $output['equity_num']; ?><!--</em>-->
<!--                    </dd>-->
<!--                </dl>-->
            </div>
        </div>
        <?php if (!empty($output['s_notice'])): ?>
        <div class="container type-b">
            <div class="hd">
                <h3>系统公告</h3>
            </div>
            <div class="content">
                <ul class="notice">
                    <?php foreach ($output['s_notice'] as $notice): ?>
                    <li style="display: block">
                        <a href="index.php?act=notice&id=<?php echo $notice['notice_id']; ?>">
                            <?php echo $notice['notice_title']; ?>
                        </a>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
        <?php endif;?>
        <div class="container type-a" style="width: 920px">
            <div class="hd">
                <h3>我的推广二维码</h3>
            </div>
            <div class="content">
                <dl style="margin-left: 300px;">
                    <dt>
                        <img src="<?php echo $output['info']['qr_url']; ?>" style="width: 100px;height: 100px;background-color: blue">
                    </dt>
                </dl>
                <dl style="margin-left: 300px;">
                    <dt>
                        <em style="font-size: 15px">推广链接:<?php echo $output['info']['url']; ?></em>
                    </dt>
                </dl>
            </div>
        </div>
    </div>
</div>
