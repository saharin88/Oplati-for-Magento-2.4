require(['jquery', 'mage/cookies', 'mage/translate'], function ($) {

    $(document).ready(function () {

        let olatiObj = $('#oplati'),
            checkStatusTimeout = parseInt(olatiObj.data('timeout'));

        let checkStatus = function () {
            $.ajax({
                url: '//' + location.host + '/oplati/payment/checkstatus',
                data: {form_key: $.mage.cookies.get('form_key')},
                dataType: 'json',
                cache: false,
                success: function (resp) {
                    if (resp.success) {
                        if (resp.data.status === 0) {
                            setTimeout(checkStatus, checkStatusTimeout);
                        } else if (resp.data.status === 1) {
                            olatiObj.html('<p class="text-success">' + resp.message + '</p>');
                            setTimeout(function () {
                                location.href = '//' + location.host + '/oplati/payment/success';
                            }, 3000);
                        } else {
                            olatiObj.html('<p class="text-danger">' + resp.message + '</p><p><button id="rePayment" class="button">' + $.mage.__('Repeat payment') + '</button><a class="button2" href="/oplati/payment/cancel">' + $.mage.__('Cancel payment') + '</a></p>');
                        }
                    } else {
                        console.log(resp.message);
                    }
                },
                error: function () {
                    olatiObj.html($.mage.__('Response error'));
                }
            });
        };

        setTimeout(checkStatus, checkStatusTimeout);

        $('body').on('click', 'button#rePayment', function (e) {

            $('body').trigger('processStart');

            $.ajax({
                url: '//' + location.host + '/oplati/payment/repeat',
                data: {form_key: $.mage.cookies.get('form_key')},
                dataType: 'json',
                cache: false,
                success: function (resp) {

                    $('body').trigger('processStop');

                    if (resp.success) {
                        olatiObj.html($(resp.data).html());
                        setTimeout(checkStatus, checkStatusTimeout);
                    } else {
                        alert($.mage.__('Error'));
                        console.log(resp.message);
                    }
                },
                error: function () {
                    $('body').trigger('processStop');
                    olatiObj.html($.mage.__('Response error'));
                }
            });


        });

    });

});
