@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row " style="margin-bottom: 20px">
    
        <div class=" offset-md-10">
    
            <a href="{{route('publisher.form')}}" class="btn btn-primary">
                {{ __('Add Publisher') }}
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
              <th scope="col">Publisher Name</th>
              <th scope="col">Publisher Email</th>
              <th scope="col">Last Updated</th>
              <th scope="col">Created At</th>

            </tr>
        </thead>
        <tbody>
            @if(!empty($data))
                @foreach($data as $record)
                  
                    <tr>
                      <th scope="row"><a href="/publisher/detail/{{$record->id}}">{{$record->id}}</a></th>
                      <td>{{$record->name}}</td>
                      <td>{{$record->email}}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    <!-- Display pagination links -->
       {{ $data->links() }}
 
</div>



@endsection