$(function() {
    $(".keyword-info").click(function(){

        var keyWord = $(this).data("keyword");


        $.get( "/api/reviews-with-keyword/"+campaignId+"/"+startDate+"/"+endDate+"/"+keyWord, function( data ) {
            $("#reviewModal").modal();

            data.forEach( function (arrayItem)
            {
                var x = arrayItem.prop1 + 2;
            });

        });

    });
});
