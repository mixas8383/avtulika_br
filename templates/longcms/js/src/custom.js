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
console.log(hiddenInput);
console.log(lefttime);

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

    getUpdates();
    setInterval('getAjaxAutobild()', 1000);
    setInterval('getUpdates();updateTimers()', 1000);

    // setInterval('getUpdates()', 1000);




});

var updated = new Array();

function getUpdates()
{
    if (!(typeof (currentPageIds) == 'object'))
    {
        return false;
    }

    if (currentPageIds.length < 1)
    {
        return false;
    }

    url = '/update.php';



//    for (i = 0; i < currentPageIds.length; i++)
//    {
//        console.log(currentPageIds[i])
//    }
    url = updateURLParameter(url, 'rand', Math.random());
    $.ajax({
        url: url,
        data: {ids: currentPageIds.join('|')},
        dataType: 'json',
        success: function (data) {



            if (data.state) {


                if (updated.length == 0)
                {
                    if (data.data.length > 0)
                    {
                        for (i = 0; i < data.data.length; i++)
                        {
                            updated['ob' + data.data[i].id] = data.data[i];
                        }
                        updated.length = data.data.length;

                    }
                } else
                {
                    if (data.data.length > 0)
                    {

                        for (i = 0; i < data.data.length; i++)
                        {
                            if (updated['ob' + data.data[i].id] != undefined)
                            {
                                checkChange(updated['ob' + data.data[i].id], data.data[i])

                            }

                        }
                    }

                }

            }
        }
    })




}

function checkChange(oldObj, newObj)
{

    st = false
    if (oldObj.total_bids != newObj.total_bids)
    {
        st = true;
    }
    if (oldObj.activeLot != newObj.activeLot)
    {
        st = true;
    }

    if (st)
    {
        updated['ob' + newObj.id] = newObj
        makeBidChange(newObj)

    }



}


function makeBidChange(obj)
{
    console.log('chek');

    if (obj.leftTime > 10 && obj.activeLot)
    {
        obj.leftTime = 10
    }
    if (obj.leftTime > 0) {
       $('#timerCounterHidden_' + obj.id).val(obj.leftTime);
    }
    $('.timerCounter_' + obj.id).css('background-color', 'yellow');
    $('.itemPrice_' + obj.id).html(obj.total_bids / 100);
    $('.deal_user_' + obj.id).html(obj.username);
    setTimeout("$('.timerCounter_" + obj.id + "').css('background-color','');", 200)






    console.log('change visual')
}