@include("campaign.header")
<div class="container">

    <div class="container">
        <div class="row ">
            <div class="col-xs-12">
                <div class="jumbotron">
                    <h1>{{$campaign->business_name}}</h1>
                    <h3> An overview of your business's performance</h3>
                    </br>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="row">
            <br>
            <div class="col-xs-8">
                <h3>Overall Satisfaction:</h3>
                <br>
                <div class="progress">
                    <div class="progress-bar progress-bar-success"
                         style="width: {{$percentages['averagePercentage']}}%">
                        {{$percentages['averagePercentage']}}%
                        <span class="sr-only">{{$percentages['averagePercentage']}}{{$percentages['averagePercentage']}}
                            % Satisfaction</span>
                    </div>
                    <div class="progress-bar progress-bar-danger"
                         style="width: {{$percentages['remainingPercentage']}}%">
                        {{$percentages['remainingPercentage']}}%
                        <span class="sr-only">{{$percentages['remainingPercentage']}}% Dis-Satisfaction</span>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class=" container">
        <div class="row">
            <div class="col-xs-6">
                <h3 id="h31">Specific Areas</h3>
                <br>
            </div>

            <div class="col-xs-6">

                <h3 id="h32">Pie Charts</h3>


            </div>

        </div>
    </div>


    <div class='container-fluid'>

        @foreach ($keywordsWithSentiment->slice(0,6) as $keyword)


            <div class="col-xs-6">
                <h3>{{ $keyword->word }}:</h3>

                <div class="progress">
                    <div class="progress-bar" role="progressbar"
                         aria-valuenow="{{ round($keyword->average_sentiment) }}"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         style="width: {{round($keyword->average_sentiment)}}%;">
                        {{ round($keyword->average_sentiment) }}%
                    </div>
                </div>
            </div>
        @endforeach


    </div>


    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="well">

                    <h1>Trend of Satisfaction</h1>

                </div>
            </div>
        </div>
    </div>


    <div class="container">

        <div class="row">
            <div class="col-xs-12">
                <div class="jumbotron">
                    <div id="curve_chart"></div>

                </div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <div class="well">

                    <h1>Latest Reviews</h1>

                </div>
            </div>
        </div>
    </div>


    <div class="container">


        @foreach($latestReviews as $review)
            <div class="row">
                <div class="col-xs-12">
                    <div class="jumbotron">
                        <p>"{{ $review->text }}"</p>
                        <span>{{ $review->sentiment() }}% Positive</span>
                        <span> - {{ $review->author }}</span>
                        <span class="right" style="float:right;">{{ $review->rating }}
                            % - {{ Carbon\Carbon::parse($review->date)->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>

        @endforeach
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>

<script>
    var jsonData = {!! $weeksDataJson !!};
</script>


<script src="/js/overall_line_chart.js"></script>

</body>


</html>
