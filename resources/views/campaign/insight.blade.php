@include("campaign.header")

<div class="pageHeader">
    <div class="row ">
        <div class="col-xs-12">
            <div class="jumbotron text-center">
                <h1>{{$campaign->business_name}}</h1>
                <h3> View your businesses performance for different periods</h3>
                <br/>
            </div>
        </div>
    </div>
</div>


<div class="pageBody">

    <div class="container" >
        <div class="row">

            <div class="col-xs-12">


                <ol class="breadcrumb">
                    <span class="glyphicon glyphicon-menu-right"></span>
                    <li class="breadcrum-item"><a href="/dashboard">Dashboard</a></li>
                    <li class="breadcrum-item"><a href="/campaign/{{$campaign->id}}">Overview</a></li>
                    <li class=" breadcrumb-item active">{{$campaign->business_name}}</li>
                </ol>

            </div>
        </div>
    </div>






</div>





<div class="container">
    <h2  class="page-header">Satisfaction per week:</h2>

    <div class="row">
        <div class="col-xs-12">
            <div class="jumbotron">
                <div class="row">
                    <div class="col-xs-10">
                        <div id="chart_div"></div>
                    </div>
                    <div col="col-xs-2">
                        <div id="colFilter_div"></div>
                    </div>
                </div>
                <div class="col-xs-12" id="dateSlider_div"></div>


            </div>
        </div>
    </div>
</div>





</div>


<!-- Modal -->
<div class="modal fade" id="reviewModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Reviews for keyword</h4>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow: auto">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script>

    var campaignId = {{$campaign->id}};
    var columns = {!!$columns!!};
    var chartData = {!!$chartData!!};

</script>


<script src="/js/in_depth_line_chart.js"></script>
<script src="/js/dashboard_interactions.js"></script>
</body>



</html>

