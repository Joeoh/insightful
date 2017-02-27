<h1>Create Campaign</h1>

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
    Business name:<br>
    <input type="text" name="business_name" value=""><br>
    Yelp Slug - (The end of the yelp URL):<br>
    <input type="text" name="yelp_slug" value=""><br><br>
    <input type="submit" value="Submit">
</form>