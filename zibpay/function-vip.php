<?php

/**
 * 子比主题
 * 用户VIP系统
 */



/**用户中心 */
function zibpay_user_vip_tab_con($user_id)
{

?>
    <div class="tab-pane fade in active" id="author-tab-vip">
        <div class="theme-box user-pay">
            <div class="box-body">
                <?php

                echo zibpay_user_vip_box($user_id);

                ?>
            </div>
        </div>
    </div>
<?php
}

function zibpay_user_vip_box($user_id)
{
    $vip_level = get_user_meta($user_id, 'vip_level', true);
    $vip_exp_date = get_user_meta($user_id, 'vip_exp_date', true);
    $zero1 = current_time("Y-m-d h:i:s");
    if ($vip_level) {
        if ($vip_exp_date == 'Permanent') {
            echo '<div class="title-h-left"><b>我的会员</b></div>';
            echo '<div class="muted-2-color c-red">已开通' . _pz('pay_user_vip_' . $vip_level . '_name') . '，永久有效</div>';
            echo '<div class="row mt10">';
            echo '<div class="col-sm-8">';
            echo zibpay_get_viped_card($vip_level);
            echo '</div>';
            echo '</div>';
        } elseif (strtotime($zero1) < strtotime($vip_exp_date)) {
            echo '<div class="title-h-left"><b>我的会员</b></div>';
            echo '<div class="muted-2-color c-red">已开通' . _pz('pay_user_vip_' . $vip_level . '_name') . '，到期时间：' . current_time("Y年m月d日", strtotime($vip_exp_date)) . '</div>';
            echo '<div class="row mt10">';
            echo '<div class="col-sm-8">';
            echo zibpay_get_viped_card($vip_level);
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="title-h-left"><b>续费会员</b></div>';
            echo '<div class="muted-2-color c-red">您的' . _pz('pay_user_vip_' . $vip_level . '_name') . '已过期，过期时间：' . current_time("Y年m月d日", strtotime($vip_exp_date)) . '</div>';
        }
    } else {
        echo '<div class="title-h-left"><b>开通会员</b></div><div class="muted-2-color c-red">' . _pz('pay_user_vip_desc') . '</div>';

        echo '<div class="row mt10">';
        echo '<div class="col-sm-6">';
        echo zibpay_get_vip_card(1);
        echo '</div>';
        echo '<div class="col-sm-6">';
        echo zibpay_get_vip_card(2);
        echo '</div>';
        echo '</div>';
    }
}

function zibpay_pay_uservip_modal()
{
    if (!is_user_logged_in()) {
        echo (json_encode(array('error' => 1, 'ys' => 'danger', 'msg' => '请先登录')));
        exit();
    }

    $vip_level = !empty($_POST['vip_level']) ? $_POST['vip_level'] : 1;

    global $current_user;
    $avatar = zib_get_data_avatar($current_user->ID);
    $user_name = $current_user->display_name;
    $vip_desc = _pz('pay_user_vip_desc');


    $user_info = '';
    $vip_more = '<div class="muted-2-color mb10 em09">' . _pz('pay_user_vip_more') . '</div>';
    $mark = _pz('pay_mark', '￥');
    $mark = '<span class="pay-mark">' . $mark . '</span>';
    $payment = zibpay_get_default_payment();

    $tab_c = '';
    $tab_t = '';

    for ($vi = 1; $vi <= 2; $vi++) {
        if (!_pz('pay_user_vip_' . $vi . '_s', true)) {
            continue;
        }
        $card_args = array();
        $tab_t .= '<li class="relative ' . ($vip_level == $vi ? ' active' : '') . '">
        <a class="" data-toggle="tab" href="#tab-payvip-' . $vi . '">' . zibpay_get_vip_card_mini($vi, $card_args) . '</a><div class="abs-right active-icon"><i class="fa fa-check-circle" aria-hidden="true"></i></div>
    </li>';

        $vip_icon = '<div class="payvip_icon mb10"><p>' . zibpay_get_vip_card_icon($vi) . '</p>' . _pz('pay_user_vip_' . $vi . '_name') . '</div>';
        $vip_equity = '<ul class="payvip_equity mt10">' . _pz('pay_user_vip_' . $vi . '_equity') . '</ul>';

        $vip_product = '';
        for ($i = 1; $i <= 4; $i++) {

            if (_pz('vip_product_' . $vi . '_' . $i . '_s')) {
                $price = round(_pz('vip_product_' . $vi . '_' . $i . '_price'), 2);

                $show_price = round(_pz('vip_product_' . $vi . '_' . $i . '_show_price'), 2);
                $show_price = $show_price ? '<span class="original-price ml10 relative">' . $mark . $show_price . '' : '</span>';

                $price = '<div class="product-price c-red">' . $mark . $price . $show_price . '</div>';

                $vip_time = (int) _pz('vip_product_' . $vi . '_' . $i . '_time');

                $vip_tag = _pz('vip_product_' . $vi . '_' . $i . '_tag');
                $vip_tag = $vip_tag ? '<div class="abs-right vip-tag">' . $vip_tag . '</div>' : '';

                if ($vip_time) {
                    $vip_time = $vip_time . '个月';
                } else {
                    $vip_time = '永久';
                }
                $vip_time = '<div class="muted-2-color">' . $vip_time . '</div>';

                $vip_product .= '<label>
                <input class="hide vip-product-input" type="radio" name="vip_product_id" value="' . $vi . '_' . $i . '"' . ($i == 1 ? ' checked="checked"' : '') . '>
                <div class="zib-widget vip-product relative text-center product-box">
                ' . $vip_tag . $price . $vip_time . '
                </div></label>';
            }
        }

        $order_name = get_bloginfo('name') . '-开通' . _pz('pay_user_vip_' . $vi . '_name');
        $payvip_form = '<input type="hidden" name="order_name" value="' . $order_name . '">
        <input type="hidden" name="order_type" value="4">
        <input type="hidden" name="action" value="initiate_pay">';

        $pay_button = '<button class="but jb-yellow padding-lg initiate-pay" pay_type="' . $payment . '"><i class="fa fa-angle-right mr10" aria-hidden="true"></i>确认支付</button>';

        $tab_c .= '<div class="tab-pane fade' . ($vip_level == $vi ? ' active in' : '') . '" id="tab-payvip-' . $vi . '">
        <form>
        <div class="row">
            <div class="col-sm-5 text-center theme-box">' . $vip_icon . $vip_equity . '

            </div>
            <div class="col-sm-7"><div class="mb10">' . $vip_product . '</div>' . $vip_more . $pay_button . '

            </div>
        </div>
        ' . $payvip_form . '
        </form>
    </div>';
    }
    $con .= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></button>';
    $con .= '<ul class="list-inline user-box"><li><div class="avatar-img">' . $avatar . '</div></li><li><b>' . $user_name . '</b><div class="c-red em09">' . $vip_desc . '</div></li></ul>';
    $con .= '<ul class="list-inline mt10 theme-box vip-cardminis">' . $tab_t . '</ul>';
    $con .= '<div class="tab-content mt10">' . $tab_c . '</div>';

    $con = '<div class="box-body payvip-modal">' . $con . '</div>';

    $pay_moda_args = array(
        'class' => '',
        'payment' => $payment,
        'order_price' => 0,
        'order_name' => $order_name,
    );
    $pay_modal = zibpay_qrcon_pay_modal($pay_moda_args);

    echo (json_encode(array('error' => 0, 'pay_modal' => $pay_modal, 'html' => $con)));
    exit();
}
add_action('wp_ajax_pay_vip', 'zibpay_pay_uservip_modal');
add_action('wp_ajax_nopriv_pay_vip', 'zibpay_pay_uservip_modal');

/**付款成功后后更新用户数据 */
function zibpay_uservip_paysuccess($values)
{

    if (empty($values['status']) || empty($values['order_num'])) return false;

    global $wpdb;
    $pay_order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->zibpay_order} WHERE order_num = %s", $values['order_num']));
    if (empty($pay_order->user_id) || empty($pay_order->product_id)) return false;

    $vip_product_id = explode("_", $pay_order->product_id);
    if (empty($vip_product_id[0]) || empty($vip_product_id[1]) || empty($vip_product_id[2]) || $vip_product_id[0] != 'vip') return false;
    $vip_level = (int) $vip_product_id[1];
    $vip_product = (int) $vip_product_id[2];

    $vip_time = (int) _pz('vip_product_' . $vip_level . '_' . $vip_product . '_time');
    if ($vip_time) {
        $vip_exp_date = get_user_meta($pay_order->user_id, 'vip_exp_date', true);
        if (!$vip_exp_date || $vip_exp_date == 'Permanent' || !strtotime($vip_exp_date)) {
            $now = current_time('Y-m-d H:i:s', time());
        } else {
            $now = $vip_exp_date;
        }
        $vip_exp_date = current_time("Y-m-d 23:59:59", strtotime("+" . $vip_time . "months", strtotime($now)));
    } else {
        $vip_exp_date = 'Permanent';
    }

    update_user_meta($pay_order->user_id, 'vip_level', $vip_level);
    update_user_meta($pay_order->user_id, 'vip_exp_date', $vip_exp_date);
}
add_action('payment_order_success', 'zibpay_uservip_paysuccess');


function zibpay_get_viped_card($level = 1, $args = array())
{
    $defaults = array(
        'type' => 'auto',
    );

    $args = wp_parse_args((array) $args, $defaults);

    $icon =  zibpay_get_vip_card_icon($level);
    $img =  '<div class="vip-img abs-right">' . $icon . '</div>';
    $name = '<div class="vip-name mb10"><span class="mr6">' . $icon . '</span>' . _pz('pay_user_vip_' . $level . '_name') . '</div>';

    $button = '<a class="but jb-blue radius payvip-button">开通' . _pz('pay_user_vip_' . $level . '_name') . '</a>';

    $ba_icon = '<div class="abs-center vip-baicon">' . $icon . '</div>';
    $vip_equity = '<ul class="mb10">' . _pz('pay_user_vip_' . $level . '_equity') . '</ul>';

    $card = '<div class="vip-card level-' . $level . ' ' . zibpay_get_vip_theme($level) . '" vip-level="' . $level . '">
    ' . $ba_icon . $name . $vip_equity . $img . '
    </div>';
    return $card;
}

function zibpay_get_vip_card($level = 1, $args = array())
{
    if (!_pz('pay_user_vip_' . $level . '_s', true))  return;
    $defaults = array(
        'type' => 'auto',
    );

    $args = wp_parse_args((array) $args, $defaults);

    $icon =  zibpay_get_vip_card_icon($level);

    $action_class = is_user_logged_in() ? ' pay-vip' :' signin-loader';
    $img =  '<div class="vip-img abs-right">' . $icon . '</div>';
    $name = '<div class="vip-name mb10"><span class="mr6">' . $icon . '</span>' . _pz('pay_user_vip_' . $level . '_name') . '</div>';

    $button = '<a class="but jb-blue radius payvip-button">开通' . _pz('pay_user_vip_' . $level . '_name') . '</a>';

    $ba_icon = '<div class="abs-center vip-baicon">' . $icon . '</div>';
    $vip_equity = '<ul class="mb10">' . _pz('pay_user_vip_' . $level . '_equity') . '</ul>';

    $card = '<div class="vip-card pointer level-' . $level . ' ' . zibpay_get_vip_theme($level) .$action_class. '" vip-level="' . $level . '">
    ' . $ba_icon . $name . $vip_equity . $button . $img . '
    </div>';
    return $card;
}

function zibpay_get_vip_card_mini($level = 1, $args = array())
{

    $icon =  zibpay_get_vip_card_icon($level);
    $name = '<div class="vip-icon">' . $icon . '</div><div class="vip-name">' . _pz('pay_user_vip_' . $level . '_name') . '</div>';

    $ba_icon = '<div class="abs-center vip-baicon">' . $icon . '</div>';

    $card = '<div class="vip-card vip-cardmini level-' . $level . ' ' . zibpay_get_vip_theme($level) . '">
    ' . $ba_icon . $name . '
    </div>';
    return $card;
}

function zibpay_get_payvip_button($level = 1, $class = 'but jb-yellow', $text = '立即开通')
{
    $button = '<a class="pay-vip ' . $class . '" href="" vip-level="' . $level . '">' . $text . '</a>';
    return $button;
}

function zibpay_get_vip_card_icon($level = 1, $class = "icon em12", $tip = false)
{
    $icon = $level == 1 ? '<i class="fa fa-star-o" aria-hidden="true"></i>' : '<i class="fa fa-diamond" aria-hidden="true"></i>';
    return $icon;
}

function zibpay_get_vip_icon($level = 1, $class = "em12 ml3", $tip = 1)
{
    if (!$level) return;
    $icon = zib_svg('vip_' . $level, '0 0 1024 1024', $class);
    return $tip ? '<sapn data-toggle="tooltip" title="' . _pz('pay_user_vip_' . $level . '_name') . '">' . $icon . '</sapn>' : '<sapn class="vip-texticon">' . $icon . _pz('pay_user_vip_' . $level . '_name') . '</sapn>';
}

function zibpay_get_payvip_icon($user_id = 0, $class = '', $tip = 1, $text = '开通会员')
{
    if (!$user_id || !_pz('pay_user_vip_1_s', true) || !_pz('pay_user_vip_2_s', true)) return;

    $current_user_id = get_current_user_id();
    $vip_level = zib_get_user_vip_level($user_id);
    if ($vip_level) {
        return zibpay_get_vip_icon($vip_level);
    } elseif ($user_id == $current_user_id) {
        $button = '<a class="pay-vip but jb-red radius payvip-icon' . $class . '" href="" vip-level="1">' . zib_svg('vip_1', '0 0 1024 1024', 'em12 mr3') . $text . '</a>';
        return $button;
    }
}

function zibpay_get_vip_theme($level = 1)
{
    $icon = $level == 1 ? 'vip-theme1' : 'vip-theme2';
    return $icon;
}

function zib_get_user_vip_level($user_id = 0)
{

    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id || !_pz('pay_user_vip_1_s', true) || !_pz('pay_user_vip_2_s', true)) return false;
    //update_user_meta($user_id, 'vip_level', 0);
    //update_user_meta($user_id, 'vip_exp_date', '');
    $vip_level = get_user_meta($user_id, 'vip_level', true);
    $vip_exp_date = get_user_meta($user_id, 'vip_exp_date', true);
    $zero1 = current_time("Y-m-d h:i:s");
    if ($vip_level && $vip_exp_date == 'Permanent') return $vip_level;
    return ($vip_level && (strtotime($zero1) < strtotime($vip_exp_date))) ? $vip_level : false;
}

function zib_get_user_vip_exp_date_text($user_id = 0)
{

    if (!$user_id) $user_id = get_current_user_id();
    if (!$user_id || !_pz('pay_user_vip_1_s', true) || !_pz('pay_user_vip_2_s', true)) return false;

    $vip_exp_date = get_user_meta($user_id, 'vip_exp_date', true);
    $zero1 = current_time("Y-m-d h:i:s");
    if (!$vip_exp_date) return false;

    if ($vip_exp_date == 'Permanent') return '永久会员';
    return ((strtotime($zero1) < strtotime($vip_exp_date))) ? current_time("Y年m月d日", strtotime($vip_exp_date)): '会员已过期';
}

