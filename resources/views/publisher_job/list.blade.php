@extends('layouts.app')

@section('content')


<div class="container">
    @if($user_type == 'admin')
        <div class="row " style="margin-bottom: 20px">

            <div class=" offset-md-10">
                <a href="{{route('publisher.job.form')}}" class="btn btn-primary">
                    {{ __('Assign Publisher Job') }}
                </a>
            </div>
        </div>
    @endif
     @if( !empty($success))
        <div class="alert alert-success" role="alert">
            New Advertiser data successfully saved.
        </div>
    @endif
    
    <table class="table table-hover">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Publisher Name</th>
              <th scope="col">Campaign name</th>
              <th scope="col">Link</th>
              <th scope="col">Target Count</th>
              <th scope="col">Tracking Count</th>
              <th scope="col">Updated At</th>
              <th scope="col">Created At</th>

            </tr>
        </thead>
        <tbody>
            @if(!empty($data))
                @foreach($data as $record)
                  
                    <tr>
                      <th scope="row">{{$record->id}}</th>
                      <td>{{$record->publisher->name}}</td>
                      <td>{{$record->campaign->campaign_name}}</td>
                      <td>{{$domain}}/search?code={{$record->proxy_url}}&q={keyword}</td>
                      <td>{{$record->target_count}}</td>
                      <td>{{$record->tracking_count}}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    <!-- Display pagination links -->
 
</div>



@endsection