$(document).ready(function()
{
    $('#statusTemplate').hide();

    $(".statuses").empty();

    loadStatuses();

    $('.status-post').submit(function(event)
    {
        event.preventDefault();

        var body = $(this).find("textarea[name='body']").val();

        publish(body);
    });

    $('#loadMoreButton').click(function(event)
    {
        event.preventDefault();

        var page = $(this).attr("value");

        loadStatuses(++page);

        $(this).attr("value", page);
    });
});

function loadStatuses(page)
{
    page = typeof page !== 'undefined' ? page : 0;

    var jqxhr = $.post('statuses',
    {
        action: 'show',
        page: page
    })
    .done(function(data)
    {
        $.each(data.statuses, function(index, value)
        {
            //make status object and show it
            var status = makeStatus(value);

            //add the status to statuses
            $(".statuses").append(status);
        });

        console.log(data)
    })
    .fail(function(data)
    {
        console.log('error');
        console.log(data);
    });
}

function publish(body)
{
    var jqxhr = $.post('statuses',
    {
        action: 'publish',
        body: body
    })
    .done(function(data)
    {
        var status = makeStatus(data);

        //add the status to statuses
        $(".statuses").prepend(status);

        //emty the status body textfield
        var body = $('.status-post').find("textarea[name='body']").val('');
    })
    .fail(function(data)
    {
        console.log(data);
        console.log('error');
    });
}

function replaceIn(str, array)
{
    for(var key in array)
    {
        var template = '\{' + key + '\}';

        str = str.replace(new RegExp(template, 'g'), array[key]);
    }

    return str;
}

function makeStatus(data)
{
    var statusTemplate = $('#statusTemplate').html();

    var newData = {
        'username' : data.user.username,
        'created_at' : data.created_at,
        'body' : data.body
    }

    var string = replaceIn(statusTemplate, newData);

    return $(string).show();
}
