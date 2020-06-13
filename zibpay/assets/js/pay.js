var pay_ajax_url = zibpay_ajax_url;
var order_result = {};
var pay_inputs = {};
var _body = $('body');
var up_djyz = false;


function pay_action_ajax(data, _this) {
    console.log(data);

    // 弹出模态框
    $('.modal').modal('hide');
    modal = $('#modal_pay');
    modal.length && modal.modal('show');

    pay_ajax_notice('正在生成订单，请稍候', 'load');
    modal.length || notyf("加载中，请稍等...", "load","", "payvip_ajax");
    $.ajax({
        type: "POST",
        url: pay_ajax_url,
        data: data,
        dataType: "json",
        success: function (n) {
            console.log(n);

            n.msg && notyf(n.msg, (n.ys ? n.ys : (n.error ? 'danger' : "")),'',(modal.length ?'':'payvip_ajax'));
            modal.length || (_body.append(n.pay_modal),auto_fun(),modal = $('#modal_pay'),modal.modal('show'));

            if (n.error) {
                pay_ajax_notice((n.msg ? n.msg : "处理失败,即将刷新页面"), 'danger');
                setTimeout(function () {
                    location.reload();
                }, 2000);
            }
            n.order_name && modal.find('.pay-title').html(n.order_name);
            n.order_price && modal.find('.pay-price').html(n.order_price);
            n.payment_method && modal.find('.pay-payment').removeClass('wechat alipay').addClass(n.payment_method);

            if (n.url_qrcode) {
                qrcode_box = modal.find('.pay-qrcode img');
                order_result = n;
                qrcode_box.attr('src', n.url_qrcode).css({
                    'filter': 'blur(0)',
                    'opacity': '1',
                    'transition': 'all 0.3s ease 0.5s'
                })
                pay_ajax_notice('请扫码付款，付款成功后会自动跳转', '');
            }
            if (n.url && n.open_url) {
                window.location.href = n.url;
                window.location.reload;
            }
            if (!n.url && !n.url_qrcode) {
                pay_ajax_notice((n.msg ? n.msg : "支付配置错误"), 'danger');
            }
        }
    });
}


function pay_ajax_notice(msg, type) {
    notice_box = $('#modal_pay').find('.pay-notice .notice');
    msg = type == 'load' ? '<i class="loading mr6"></i>' + msg : msg;
    notice_box.removeClass('load warning success danger').addClass(type).html(msg);
}

_body.on("click", '.initiate-pay-switch', function (e) {
    var _this = $(this);

    pay_inputs.pay_type = _this.attr('pay_type');

    pay_action_ajax(pay_inputs, _this);
    _this.parents('.pay-payment').find('.pay-qrcode img').css({
        'filter': 'blur(5px)',
        'opacity': '.8',
        'transition': 'all 0.3s'
    });
    _this.parents('.pay-payment').removeClass('wechat alipay').addClass(pay_inputs.pay_type);
    return false;

})
_body.on("click", '.initiate-pay', function (e) {
    var _this = $(this);
    var form = _this.parents('form');
    pay_inputs = form.serializeObject();
    pay_inputs.pay_type = _this.attr('pay_type');

    pay_inputs.return_url || (pay_inputs.return_url = window.location.href);

    pay_action_ajax(pay_inputs, _this);

    // 每3秒检测一次是否支付，如果支付则刷新页面
    pay_order = setInterval(function () {
        if (order_result.order_num) {
            // console.log(order_result);
            $.ajax({
                type: "POST",
                url: pay_ajax_url,
                data: {
                    "action": "check_pay",
                    "post_id": pay_inputs.post_id,
                    "order_num": order_result.order_num,
                },
                dataType: "json",
                success: function (n) {
                    // console.log(n);
                    if (n.status == "1") {
                        pay_ajax_notice('付款成功，页面跳转中', 'success');
                        clearInterval(pay_order);
                        setTimeout(function () {
                            if ("undefined" != typeof pay_inputs.return_url && pay_inputs.return_url) {
                                window.location.href = pay_inputs.return_url;
                                window.location.reload;
                            } else {
                                location.reload();
                            }
                        }, 500);
                    }
                }
            });
        }
    }, 3000);

    return !1;
})

_body.on("click", '.pay-vip', function (e) {
    var _this = $(this);

    _modal = '<div class="modal fade" id="modal_pay_uservip" tabindex="-1" role="dialog" aria-hidden="false">\
    <div class="modal-dialog" role="document">\
    <div class="modal-content">\
    <div class="loading zts"></div>\
    </div>\
    </div>\
    </div>\
    </div>';
    $("#modal_pay_uservip").length || _body.append(_modal);
    auto_fun();
    modal = $('#modal_pay_uservip');
    vip_level = _this.attr('vip-level');
    if (modal.find('.payvip-modal').length) {
        $('a[href="#tab-payvip-'+vip_level+'"]').tab('show');
        modal.modal('show');
    } else {
        notyf("加载中，请稍等...", "load", "", "payvip_ajax");
        $.ajax({
            type: "POST",
            url: pay_ajax_url,
            data: {
                "action": "pay_vip",
                "vip_level": vip_level,
            },
            dataType: "json",
            success: function (n) {
                // console.log(n);
                n.msg && notyf(n.msg, (n.ys ? n.ys : (n.error ? 'danger' : "")), "", "payvip_ajax");
                n.error || modal.find('.modal-content').html(n.html), modal.modal('show'), notyf('加载中，请稍等...', "load", 1, "payvip_ajax");
            }
        });
    }
    return !1;
})