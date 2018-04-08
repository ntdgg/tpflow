{include file='pub/base' /}
{block name="content"}
<div class="page-container">
    [FORM]
    <div class="cl pd-5 bg-1 bk-gray">
        <span class="l">
            <a href="javascript:;" onclick="layer_show('新增','{:url('add')}','850','500')" class="btn btn-primary radius">新增</a>
        </span>
        <span class="r pt-5 pr-5">
            共有数据 ：<strong>{$count ?? '0'}</strong> 条
        </span>
    </div>
    <table class="table table-border table-bordered table-hover table-bg mt-20">
        <thead>
        <tr class="text-c">
            [TH]
            <th width="70">操作</th>
        </tr>
        </thead>
        <tbody>
        {volist name="list" id="vo"}
        <tr class="text-c">
            [TD]
            <td class="f-14">
					[FLOW]
					<span class="btn  radius size-S" onclick="layer_show('修改','{:url('edit',['id'=>$vo.id])}','850','500')">修改</span>
            </td>
        </tr>
        {/volist}
        </tbody>
    </table>
    <div class="page-bootstrap">{$page ?? ''}</div>
</div>
{/block}
[SCRIPT]
