@include("campaign.header")

<div class="container">
    <div class="container">

        <h1>Campaigns</h1>

        @foreach($campaigns->chunk(3) as $campaignsChunk)
            <div class="row">
                @foreach($campaignsChunk as $campaign)

                <div class="col-xs-4">
                    <div class="jumbotron">
                        <h3>{{ $campaign->business_name }}</h3>
                        <span><a href="/campaign/{{ $campaign->id }}">Details</a></span>
                    </div>
                </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>