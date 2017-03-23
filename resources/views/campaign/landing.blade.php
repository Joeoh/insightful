<head>

</head>

{{$campaign->business_name}} / {{$campaign->yelp_slug}} / {{$numReviews}} Reviews

<h2>Average Sentiment for all reviews: {{$averageSentiment}}</h2> <br>
<h2>Average Sentiment for last two weeks of reviews: {{$sentimentLastTwoWeeks}}</h2>


<h2>Here are the popular keywords with sentiment</h2>
<table>
    <tr>
        <th>Word</th>
        <th>Count</th>
        <th>Sentiment</th>
    </tr>
    @foreach ($keywordsWithSentiment->slice(0,10) as $keyword)
        <tr>
            <td>
                {{ $keyword->word }}
            </td>
            <td>
                {{ $keyword->count }}
            </td>
            <td>
                {{ $keyword->average_sentiment }}
            </td>
        </tr>
    @endforeach

</table>


<h2>Latest Reviews:</h2>
<table>
    <tr>
        <th>Date</th>
        <th>Review</th>
        <th>Sentiment</th>
    </tr>
    @foreach ($latestReviews as $review)
        <tr>
            <td>
                {{ $review->date }}
            </td>
            <td>
                {{ $review->text }}
            </td>
            <td>
                {{ $review->sentiment() }}
            </td>
        </tr>
    @endforeach

</table>


<script>
    var jsonData = {!! $weeksDataJson !!};
</script>