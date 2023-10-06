@extends('layouts.app')

@section('content')


<div class="container">
<!--    <div class="row " style="margin-bottom: 20px">
    
        <div class=" offset-md-9">
             <a href="{{route('advertiser.campaign')}}" class="btn btn-primary">
                {{ __('Add Campaign') }}
            </a>
            <a href="{{route('advertiser.form')}}" class="btn btn-primary">
                {{ __('Add Advertiser') }}
            </a>
        </div>
    </div>-->
     @if( !empty($success))
        <div class="alert alert-success" role="alert">
            New Advertiser data successfully saved.
        </div>
    @endif
    
    <table class="table table-hover">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Advertiser Name</th>
              <th scope="col">Campaign Name</th>
              <th scope="col">Target Count</th>
              <th scope="col">Target Url</th>
              <th scope="col">Query String</th>
              <th scope="col">Status</th>
              <th scope="col">Last Updated</th>
              <th scope="col">Created At</th>
              <th scope="col">Edit</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($data))
                @foreach($data as $record)
                  
                    <tr>
                      <th scope="row"><a href="/tracking/campaign/detail/{{$record->id}}">{{$record->id}}</a></th>
                      <td>{{$record->advertiser->name}} ({{$record->advertiser->manual_id}})</td>
                      <td>{{$record->campaign_name}}</td>
                      <td>{{$record->target_count}}</td>
                      <td>{{$record->target_url}}</td>
                      <td>{{$record->query_string}}</td>
                      <td>{{$record->status}}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                      <td><i class="fa fa-pencil" aria-hidden="true"></i></td>
                      
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    <!-- Display pagination links -->
   {{ $data->links() }}
</div>



@endsection