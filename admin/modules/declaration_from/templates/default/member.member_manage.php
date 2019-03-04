<?php defined('In33hao') or exit('Access Invild!');?>
<div class="page">
	<div class="fixed-bar">
    	<div class="item-title">
      		<div class="subject">
        		<h3>会员管理</h3>
        		<h5>会员系统会员信息设置管理</h5>
      		</div>
    	</div>
  	</div>
  	<!-- 操作说明 -->
  	<div class="explanation" id="explanation">
    	<div class="title" id="checkZoom">
    		<i class="fa fa-lightbulb-o"></i>
      		<h4 title="提示相关设置操作时应注意的要点">操作指示</h4>
      		<span id="explanationZoom" title="收起提示"></span>
      	</div>
    	<ul>
      		<li>通过会员管理，你可以进行查看、编辑会员资料等操作</li>
      		<li>你可以根据条件搜索会员，然后选择相应的操作</li>
    	</ul>
  	</div>
  	<div id="flexigrid"></div>
    <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
    <div class="ncap-search-bar">
        <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
        <div class="title">
            <h3>高级搜索</h3>
        </div>
        <form method="get" name="formSearch" id="formSearch">
            <div id="searchCon" class="content">
                <div class="layout-box">
                    <dl>
                        <dt>关键字搜索</dt>
                        <dd>
                            <label>
                                <select class="s-select" name="keyword_type">
                                    <option selected="selected" value="">-请选择-</option>
                                    <option value="member_mobile">会员手机</option>
                                    <option value="member_name">真实姓名</option>
                                </select>
                            </label>
                            <label>
                                <input type="text" value="" placeholder="请输入关键字" name="keyword" class="s-input-txt">
                            </label>
                        </dd>
                    </dl>
                    <dl>
                        <dt>会员状态</dt>
                        <dd>
                            <label>
                                <select name="member_state" class="s-select">
                                    <option value="">-请选择-</option>
                                    <option value="0">冻结</option>
                                    <option value="1">正常</option>
                                </select>
                            </label>
                        </dd>
                    </dl>
                    <dl>
                        <dt>注册时间</dt>
                        <dd>
                            <label>
                                <input readonly id="query_start_date" placeholder="请选择起始时间" name=query_start_date value="" type="text" class="s-input-txt" />
                            </label>
                            <label>
                                <input readonly id="query_end_date" placeholder="请选择结束时间" name="query_end_date" value="" type="text" class="s-input-txt" />
                            </label>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="bottom"><a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green mr5">提交查询</a><a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search']; ?></a></div>
        </form>
    </div>
</div>
<script type="text/javascript">
$(function() {
    $("#flexigrid").flexigrid({
        url: 'index.php?act=member&op=get_xml',
        colModel : [
            {display: '操作', name : 'operation', width : 100, sortable : false, align: 'center', className: 'handle-s'},
            {display: '会员手机', name : 'member_mobile', width : 130, sortable : false, align: 'center'},
            {display: '真实姓名', name : 'member_name', width : 130, sortable : false, align: 'center'},
            {display: '推荐人手机', name : 'invite_mobile', width : 130, sortable : false, align: 'center'},
            // {display: '接点人手机', name : 'access_mobile', width : 160, sortable : false, align: 'center'},
            {display: '注册时间', name : 'member_time', width : 130, sortable : false, align: 'center'},
            {display: '会员状态', name : 'member_state', width : 130, sortable : false, align: 'center'},
            {display: '会员等级', name : 'customer_level', width : 130, sortable : false, align: 'center'},
            // {display: '接点族谱图', name : 'access_family_tree', width : 160, sortable : false, align: 'center'},
            // {display: '会员系统登录', name : 'direct_login', width : 160, sortable : false, align: 'center'},
            {display: '推荐族谱图', name : 'invite_family_tree', width : 130, sortable : false, align: 'center'},
            ],
        buttons : [
            {display: '<i class="fa fa-plus"></i>新增会员', name : 'add', bclass : 'add', title : '新增会员数据', onpress : fg_operation },
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'xls', bclass : 'xls', title : '将选定行数据导出execl文件', onpress : fg_operation },
            {display: '<b style="color: red" id="strategic_partner" "></b>',},//战略合伙人
            {display: '<b style="color: red" id="senior_partner" "></b>'},//高级合伙人
            {display: '<b style="color: red" id="partner" "></b>'},//合伙人
            {display: '<b style="color: red" id="shopkeeper" "></b>'},//店主
            {display: '<b style="color: red" id="vip" "></b>'},//VIP
            {display: '<b style="color: red" id="experience" "></b>'},//体验用户

        ],
        searchitems: [
            {display: '会员手机', name : 'member_mobile'},
            {display: '推荐人手机', name: 'invite_mobile'},
            ],
        sortname: "member_time",
        sortorder: "desc",
        title: '会员列表'
    });
    $(".nBtn").after("<div style='line-height: 26px'><a href='index.php?act=member&op=invite_family_tree&mobile=13800138000'><i class='fa fa-pencil-square-o'></i><b>推荐族谱图</b></a></div>");
});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=member&op=member_add';
    }
    if (name == 'xls') {
        if ($('.trSelected', bDiv).length == 0) {
            if (!confirm('您确定要下载全部数据吗？')) {
                return false;
            }
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_xls(itemids);
    }
}

function fg_xls(ids) {
    id = ids.join(',');
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_xls&id='+ id;
}
// 高级搜索提交
$('#ncsubmit').click(function() {
    $("#flexigrid").flexOptions({ url: 'index.php?act=member&op=get_xml&' + $("#formSearch").serialize(), query: '', qtype: '' }).flexReload();
});

// 高级搜索重置
$('#ncreset').click(function() {
    $("#flexigrid").flexOptions({ url: 'index.php?act=member&op=get_xml' }).flexReload();
    $("#formSearch")[0].reset();
});
$('#query_start_date').datepicker();
$('#query_end_date').datepicker();
</script>
<script>
    //添加各会员等级人数到页面
    $(function () {
        $.post(
            "<?php echo C('base_site_url');?>/admin/modules/member_center/index.php?act=member&op=getMemberLevelNum",
            function (res) {
                var data = eval("("+res+")")
                $("#strategic_partner").text("战略合伙人:"+data.strategic_partner)
                $("#senior_partner").text("高级合伙人:"+data.senior_partner)
                $("#partner").text("合伙人:"+data.partner)
                $("#shopkeeper").text("店主:"+data.shopkeeper)
                $("#vip").text("VIP:"+data.vip)
                $("#experience").text("体验用户:"+data.experience)
                // console.log(data)
            }
        )
    })
</script>