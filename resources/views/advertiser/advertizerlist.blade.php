@extends('layouts.app')

@section('content')


<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="javascript:void(0)">Advertizer List</a></li>
         
        </ol>
    </nav>
    <div class="row " style="margin-bottom: 20px">
    
        <div class=" offset-md-10">
    
            <a href="{{route('advertiser.form')}}" class="btn btn-primary">
                {{ __('Add New Advertizer') }}
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
              <th scope="col">Advertizer Name</th>
              <th scope="col">Advertizer Email</th>
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
                      <td>{{$record->name}}</td>
                      <td>{{$record->manual_email}}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                      <td><a href="{{route('advertiser.form', ['adv_id' => $record->id])}}"><i class="fa fa-edit"> </i></a>
                          <a style="margin-left: 12px;" href="{{route('campaign.list', ['advertizer' => $record->id])}}"><i class="fa fa-eye"> </i></a>
                      </td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
       {{ $data->links() }}
 
</div>



@endsection