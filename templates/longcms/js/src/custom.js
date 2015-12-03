/*start toplink*/
jQuery.fn.topLink = function (settings) {
    settings = jQuery.extend({
        min: 1,
        fadeSpeed: 400
    }, settings);
    return this.each(function () {
        //listen for scroll
        var el = $(this);
        el.hide(); //in case the user forgot

        $(window).scroll(function () {
            var bgg = $(document).height();
            var gbb = $(document).scrollTop();
            var pgg = $(window).height();
            var frr = bgg - pgg;
            var gbb = $(document).scrollTop();
            if (frr == gbb)
            {
                $("#top-link").css({bottom: "140px"});
            }
            else
            {
                $("#top-link").css({bottom: "140px"});
            }
            ;

            if ($(window).scrollTop() >= settings.min)
            {

                el.fadeIn(settings.fadeSpeed);
            }
            else
            {
                el.fadeOut(settings.fadeSpeed);
            }
        });
    });
};
//usage w/ smoothscroll

function getAjaxAutobild()
{
    jQuery.ajax('/job.php');
}

function updateTimers()
{

    $('.timerCounter').each(function (key, value) {


        hiddenInput = $(value).find('.timerCounterHidden');

        lefttime = hiddenInput.val();


        formatedTime = formatTime(lefttime - 1);
        hiddenInput.val(lefttime - 1);
        replacementBlock = $(value).find('.innerBlockTimer');

        replacementBlock.html('<span class="timer_counter_h">' + formatedTime.h + '</span> : <span class="timer_counter_m">' + formatedTime.m + '</span> : <span class="timer_counter_s">' + formatedTime.s + '</span>');

        // console.log(formatTime(lefttime - 1));
    });


}
function formatTime(time)
{
    var ret = {};




    ret.h = parseInt(time / 3600);
    ret.m = parseInt((time - ret.h * 3600) / 60);
    ret.s = time - ret.h * 3600 - ret.m * 60;
    ret.h = ret.h + '';



    if (ret.h.length < 2)
    {
        ret.h = '0' + '' + ret.h;
    }
    ret.m = ret.m + '';
    if (ret.m.length < 2)
    {
        ret.m = '0' + '' + ret.m;
    }
    ret.s = ret.s + '';
    if (ret.s.length < 2)
    {
        ret.s = '0' + '' + ret.s;
    }

    return ret;
}

function makeUserBid(url)
{
    url = updateURLParameter(url, 'rand', Math.random());
    console.log(url)
    $.ajax({
        url: url,
        dataType: 'json',
        success: function (data) {

            console.log(data);

        }
    })


}


$(document).ready(function () {
    $('#top-link').topLink({
        min: 200,
        fadeSpeed: 400
    });
    //smoothscroll
    $('#top-link').click(function (e) {
        e.preventDefault();
        $("html, body").animate({
            scrollTop: "0px"
        });
    });


    $('.fancybox').fancybox();


    // setInterval('getAjaxAutobild()', 1000);
    setInterval('updateTimers()', 1000);
    console.log(currentPageIds);

    console.log(typeof (currentPageIds));
    for (i = 0; i < currentPageIds.length; i++)
    {
        console.log(currentPageIds[i])
    }
getUpdates()

});


function getUpdates()
{
    if (!(typeof (currentPageIds) == 'object'))
    {
        console.log('not object');
        return false;
    }
    for (i = 0; i < currentPageIds.length; i++)
    {
        console.log(currentPageIds[i])
    }
     $.ajax({
        url: url,
        dataType: 'json',
        success: function (data) {

            console.log(data);

        }
    })


    
    
    console.log(currentPageIds)
}


