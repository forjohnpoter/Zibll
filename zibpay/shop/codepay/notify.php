<?php

/**
 * 支付宝企业异步通知
 */

header('Content-type:text/html; Charset=utf-8');

ob_start();
require dirname(__FILE__) . '/../../../../../../wp-load.php';
ob_end_clean();

if (empty($_POST)) {
    echo '非法请求';
    exit();
}

$config = zibpay_get_payconfig('codepay');


ksort($_POST); //排序post参数
reset($_POST); //内部指针指向数组中的第一个元素
$sign = '';//初始化
foreach ($_POST AS $key => $val) { //遍历POST参数
    if ($val == '' || $key == 'sign') continue; //跳过这些不签名
    if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
    $sign .= "$key=$val"; //拼接为url参数形式
}
if (!$_POST['pay_no'] || md5($sign . $config['key']) != $_POST['sign']) { //不合法的数据
    exit('fail');  //返回失败 继续补单
} else {
    //成功
    $pay = array(
        'order_num' => $_POST['pay_id'],
        'pay_type' => 'codepay',
        'pay_price' => $_POST['money'],
        'pay_num' => $_POST['pay_no'],
        'other' => '',
    );
    //实例化订单函数
    $zibpay = new Zibpay_Order;
    // 更新订单状态
    $order = $zibpay->payment_order($pay);

    echo 'success';exit();
}
exit();