Here are all your campaigns:

<table>
    <tr>
        <th>id</th><th>Business Name</th><th>Yelp Slug</th><th></th>
    </tr>
    @foreach ($campaigns as $campaign)
        <tr>
            <td>
                {{ $campaign->id }}
            </td>
            <td>
                {{ $campaign->business_name }}
            </td>
            <td>
                {{ $campaign->yelp_slug }}
            </td>
            <td>
                <a href="/campaign/{{ $campaign->id }}">View Campaign</a>
            </td>
        </tr>
    @endforeach
</table>
