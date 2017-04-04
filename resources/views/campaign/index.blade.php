@include("campaign.header")

<div class="pageHeader">
    <div class="row ">
        <div class="col-xs-12">
            <div class="jumbotron text-center">
                <h1>{{$campaign->business_name}}</h1>
                <h3> An overview of your business's performance for the last month</h3>
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
                    <li class=" breadcrumb-item active">{{$campaign->business_name}}</li>
                </ol>

            </div>
        </div>
        <div class="row">

        </div>
    </div>



    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <span class="glyphicon glyphicon-check"></span> Overall Satisfaction: </h2> </div>
        </div>



        <div class="row">

            <div class="col-xs-3">

                <p>0%</p>
            </div>
            <div class="col-xs-6">
            </div>
            <div class="col-xs-3">

                <p id="bar-tag"> 100% </p>
            </div>
        </div>


        <div class="progress progress-striped active">


            @if ($percentages['averagePercentage'] <= 0)
                <div class="progress-bar progress-bar-info" style="width: 100%">
                    No data in the last month
                    <span class="sr-only">No data for the last month</span>
                </div>
            @else


            <div class="progress-bar progress-bar-success" style="width: {{$percentages['averagePercentage']}}%">
                {{$percentages['averagePercentage']}}%
                <span class="sr-only">{{$percentages['averagePercentage']}}%</span>
            </div>

            <div class="progress-bar progress-bar-danger" style="width: {{$percentages['remainingPercentage']}}%">
                {{$percentages['remainingPercentage']}}%
                <span class="sr-only">{{$percentages['remainingPercentage']}}%</span>
            </div>
            @endif
        </div>
    </div>




    <div class=" container" >
        <br>

        <div class="row">
            <div class="col-xs-12">
                <h2  class="page-header"> Specific Areas:</h2>
                <br>
            </div>
        </div>
    </div>





    <div class="container">

        <div class="jumbotron " id="specific-areas">

            @foreach ($keywordsWithSentiment->slice(0,8)->chunk(2) as $keywordChunk)
                <div class="row">
                    @foreach($keywordChunk as $keyword)
                        <div class="col-xs-6">
                            <div class="container">
                                <div class="col-xs-10"><h3 class="keyword-text">{{ucfirst($keyword->word)}}:</h3></div>
                                <div class="col-xs-2"><h3 class="keyword-info" data-keyword="{{mb_strtolower($keyword->word)}}"><span class="glyphicon glyphicon-info-sign"></span></h3></div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar
                                  @if ($keyword->average_sentiment > 60)
                                        progress-bar-success"
                                  @elseif($keyword->average_sentiment < 45)
                                        progress-bar-danger"
                                  @else
                                        progress-bar-info"
                                  @endif



                                     role="progressbar" aria-valuenow="{{ round($keyword->average_sentiment) }}"
                                     aria-valuemin="0" aria-valuemax="100"
                                     style="width: {{ round($keyword->average_sentiment) }}%;">
                                    {{ round($keyword->average_sentiment) }}%
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            @endforeach
        </div>

    </div>





    <div class="container">
        <div class="">
            <h2  class="page-header">Satisfaction per week:  <a href="/campaign/{{$campaign->id}}/insight" class="btn btn-info pull-right" role="button">More Insights</a></h2>


        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="jumbotron">
                    <div id="curve_chart"></div>
                </div>
            </div>
        </div>
    </div>


    <div class="container reviews">

        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h2 class="page-header">
                        Latest Reviews: </h2>
                </div>
            </div>



            @foreach($latestReviews as $review)

            <div class="row">
                <div class="col-xs-12">
                    <div class="jumbotron review">
                        <p>"{{ $review->text }}"</p>
                        <span>{{ $review->sentiment() }}% Positive</span>
                        <span> - {{ $review->author }}</span>
                        @if($review->source_id == 1)
                            <span> - Yelp </span>
                        @elseif($review->source_id == 2)
                            <span> - TripAdvisor </span>
                        @endif
                        <span class="right" style="float:right;">{{ $review->rating }}% Rating - {{ Carbon\Carbon::parse($review->date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>

            @endforeach




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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script>
    var jsonData = {!! $weeksDataJson !!};

    var startDate = "{{$startDate}}";
    var endDate = "{{$endDate}}";
    var campaignId = {{$campaign->id}};
</script>


<script src="/js/overall_line_chart.js"></script>
<script src="/js/dashboard_interactions.js"></script>
</body>



</html>

