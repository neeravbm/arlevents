(function ($) {

    $(document).ready(function () {

        /*function filterName () {
         alert($(this).attr('id'));

         $('.reg_badge_block.active')
         }*/

        function validateValue(obj, uid) {
            flag = true;
            $('.reg_badge_block .form-select').each(function () {
                if (obj != this && Number($(this).val()) == Number(uid)) {
                    flag = false;
                    return;
                }
            });
            return flag ? true : false;
        }

        // Loop through all the registration badge blocks and show the ones that have valid value in Name.
        var count = 0;
        $('.reg_badge_block select.reg-badge-name').each(function (i, val) {
            console.log($(val).val());
            if ($(val).val() != "0") {
                $(val).parent().parent().addClass('active').css('display', 'block');
                count = count + 1;
            }
        });
        if (count == 0) {
            $('#rb_0').css('display', 'block');
            $('#rb_0').addClass('active');
        }

        $('.removebadge input').click(function () {
            ind = $(this).attr('rbind');
            $('#rb_' + ind).slideUp();
            $('#rb_' + ind).remove();
        });

        $('#edit-act-event-registration-badge-reg-badge-add-more').click(function () {
            ind = Number($(this).attr('rbind')) + 1;
            if (ind > 100) return;
            $('#rb_' + ind).slideDown('medium');
            $('#rb_' + ind).addClass('active');
            $(this).attr('rbind', ind);
        });

        $('.reg_badge_block .form-select').change(function () {

            uid = $(this).val();

            if (Number(uid) != 0 && !validateValue(this, uid)) {
                $(this).val(0).trigger('change');
                return alert('You have already selected this user before.');
            } else {
                pobj = $(this).closest('.reg_badge_block');
                $(pobj).find('.company').val($('input[type=hidden]#uid_' + uid).attr('company'));
                $(pobj).find('.location').val($('input[type=hidden]#uid_' + uid).attr('location'));
            }
        });

        if ($('.reg_badge_block .form-select').length && !$('.messages.error').length) {
            uid = $('.reg_badge_block .form-select').val();
            if (uid == 0) {
                $('.reg_badge_block .form-select option:eq(1)').attr('selected', 'selected').trigger('change');
            }
        }

    });


})(jQuery);
