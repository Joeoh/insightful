@include("campaign.header")

<div class="pageHeader">
    <div class="row ">
        <div class="col-xs-12">
            <div class="jumbotron">
                <h1>New Campaign </h1>
                <h3>Create a new Campaign for one of your Businesses </h3>
            </div>
        </div>
    </div>
</div>

    <div class="container">

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form action="" method="post">
            {{ csrf_field() }}
            <div class="form-group">
                <label class="form-control-label" for="business_name">Business Name:</label>
                <input type="text" class="form-control form-control-danger" name="business_name" id="business_name">
            </div>
            <div class="form-group">
                <label class="form-control-label" for="yelp_slug">Yelp Slug - (The end of the Yelp URL):</label>
                <input type="text" class="form-control" id="yelp_slug" name="yelp_slug">
                <!--<span class="help-block">Invalid Address!</span>!-->
                <small class="form-text text-muted">For example if your business URL is
                    <mark>https://www.yelp.ie/biz/my-business</mark>
                    , then enter
                    <mark>my-business</mark>
                </small>
            </div>
            <div class="form-group">
                <label class="form-control-label" for="tripadvisor_slug">TripAdvisor Slug -
                    (The end of the TripAdvisor URL):</label>
                <input type="text" class="form-control" id="tripadvisor_slug" name="tripadvisor_slug">
                <!--<span class="help-block">Invalid Address!</span>!-->
                <small class="form-text text-muted">For example if your business URL is
                    <mark>https://www.tripadvisor.ie/Hotel_Review-My_Hotel.html</mark>
                    , then enter
                    <mark>Hotel_Review-My_Hotel.html</mark>
                </small>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
                    integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
                    crossorigin="anonymous"></script>
        </form>
    </div>

</body>


</html>