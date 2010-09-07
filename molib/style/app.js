var x_current_pressed;

$(document).ready(function () {
    $('div.x-button').click(function () {
        x_current_pressed = $(this)

        $('.x-buttom').removeClass('x-button-pressed');

        $(this).addClass('x-button-pressed');

        setTimeout(function () {
            x_current_pressed.removeClass('x-button-pressed');
        }, 200);
    });

    $('#x-mask').click(function () {
        $(this).css('display', 'none');
        $('.popup-message').css('display', 'none')
        $('.x-popup-message').css('display', 'none');
    });

    $(window).resize(function () {
        setTimeout(function () {
            $('#x-mask').css({'height': $(document).height()});

            $('.x-popup-message').css('top', $(document).scrollTop() + $(window).height() * 0.1);
            $('#loading').css('top', $(document).scrollTop() + $(window).height() / 2 - 24);
        }, 1000);
    });

    $(document).scroll(function () {
        if ($('.x-popup-message').css('display') == 'none')
        {
            $('.x-popup-message').css('top', $(document).scrollTop() + $(window).height() * 0.1);
        }

        $('#loading').css('top', $(document).scrollTop() + $(window).height() / 2 - 24);
    });
});

function showXMask()
{
    $('#x-mask').css({'display': '', 'height': $(document).height()});
}

var loading_timer;
var loading_bg_count = 12;

function showLoading()
{
    $('#loading').fadeIn();

    loading_timer = setInterval(function () {
        loading_bg_count = loading_bg_count - 1;

        $('#loading_box').css('background-position', '0px ' + loading_bg_count * 40 + 'px');

        if (loading_bg_count == 1)
        {
            loading_bg_count = 12;
        }
    }, 100)
}

function hideLoading()
{
    $('#loading').fadeOut();

    clearInterval(loading_timer);
}

var get_entry_page = 1;

function getEntry()
{
    showLoading();

    get_entry_page++;

    $.get('newlist.php?page=' + get_entry_page, function (data) {
        hideLoading();

        if (data == 'empty')
        {
            alert('没有更多单词了.');

            $('#x-button-get-entry').hide();
        }
        else
        {
            $('#entry_data').html($('#entry_data').html() + data);
        }
    });
}
