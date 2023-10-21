@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row " style="margin-bottom: 20px">
    
        <div class=" offset-md-10">
             <a href="{{route('advertiser.campaign')}}" class="btn btn-primary">
                {{ __('Add New Campaign') }}
            </a>
        </div>
    </div>
     @if( !empty($success))
        <div class="alert alert-success" role="alert">
            New Advertiser data successfully saved.
        </div>
    @endif
    
    <table class="table table-hover">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Advertiser Id</th>
              <th scope="col">Advertiser Name</th>
              <th scope="col">Campaign Name</th>
              <th scope="col">Target Count</th>
              <th scope="col">Target Url</th>
            
              <th scope="col">Status</th>
              <th scope="col">Last Updated</th>
              <th scope="col">Created At</th>
              <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($data))
                @foreach($data as $record)
                  
                    <tr>
                      <th scope="row">{{$record->id}}</th>
                      <td>{{$record->advertiser->id}}</td>
                      <td>{{$record->advertiser->name}} ({{$record->advertiser->manual_email}})</td>
                      <td>{{$record->campaign_name}}</td>
                      <td>{{$record->target_count}}</td>
                      <td>{{$record->target_url}}</td>
                      <td>{{ ($record->status == 1)? 'Active': (($record->status == 2)? 'Paused': 'Completed') }}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                      <td>
                          <a href="{{route('advertiser.detail', ['id' => $record->id])}}"><i class="fa fa-edit"></i></a>
                          @if($record->status == 1 || $record->status == 2)
                            <a style="margin-left:11px" href="{{route('publisher.job.form', ['campaign_id' => $record->id])}}"><i class="fa fa-tasks" aria-hidden="true"></a></i>
                          @endif
                      </td>
                      
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    <!-- Display pagination links -->
   {{ $data->links() }}
</div>



@endsection