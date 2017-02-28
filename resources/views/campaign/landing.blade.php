{{$campaign->business_name}} / {{$campaign->yelp_slug}} / {{$numReviews}} Reviews

<h2>Average Sentiment for all reviews: {{$averageSentiment}}</h2> <br>
<h2>Average Sentiment for last two weeks of reviews: {{$sentimentLastTwoWeeks}}</h2>

<h2>Here are the popular keywords</h2>
<table>
    <tr>
        <th>Word</th>
        <th>Count</th>
    </tr>
@foreach ($popularKeywords as $popularKeyword)
    <tr>
        <td>
            {{ $popularKeyword->word }}
        </td>
        <td>
            {{ $popularKeyword->count }}
        </td>
    </tr>
@endforeach

</table>
