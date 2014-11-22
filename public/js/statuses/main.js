$(document).ready(function()
{
    showStatuses();

    $('.status-post').submit(function(event)
    {
        event.preventDefault();

        body = $(this).find("textarea[name='body']").val();

        publish(body);
    });
});

function showStatuses()
{
    var jqxhr = $.post('statuses',
        {
            action: 'show'
        })
        .done(function(data)
        {
            $("#statuses").empty()

            $.each(data.statuses, function(index, value)
            {
                $("#statuses").append("<h4>" + value.body + "</h4>")
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
            showStatuses();
        })
        .fail(function()
        {
            console.log('error');
        });
}
