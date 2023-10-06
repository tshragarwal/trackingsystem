@extends('layouts.app')

@section('content')



<div class="container">
    <div class="card card-body" style="margin-bottom: 20px">
        <form action="/tracking/report/list" method='get'>
          <div class="row">
            <div class="col">
              <input type="text" class="form-control" name="subid" value="{{$query_string['subid']??''}}" placeholder="Enter Subid" aria-label="Subid">
            </div>

            <div class="col">
              <input type="text" class="form-control" name="start_date" value="{{$query_string['start_date']??''}}" placeholder="Start Date Format(2023-09-09)" aria-label="start date">
            </div>
              <div class="col">
              <input type="text" class="form-control" name="end_date" value="{{$query_string['end_date']??''}}" placeholder="End Date Format(2023-09-09)" aria-label="End date">
            </div>

            <div class="col">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>

        </form>
    </div>
    
    <div style="margin-bottom: 20px;text-align: end;">
         <form action="/tracking/report/download" method='get'>
          <div class="row">
            <div class="col">
                <input type='hidden' name='query_string' value="{{http_build_query($query_string)}}" />
                <button target="_blank" type="submit" class="btn btn-success">DOWNLOAD CSV</button>
            </div>
        </div>

        </form>
    </div>
    <table class="table table-hover">
        <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">subid</th>
              <th scope="col">Total Searches</th>
              <th scope="col">Monetized Searches</th>
              <th scope="col">ad_clicks</th>
              <th scope="col">date</th>
              <th scope="col">ctr</th>
              <th scope="col">cpc</th>
              <th scope="col">rpm</th>
              <th scope="col">revenue</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($data))
                @foreach($data as $record)
                  
                    <tr>
                        <th scope="row">{{$record->id}}</th>
                      <td>{{$record->subid}}</td>
                      <td>{{$record->total_searches}}</td>
                      <td>{{$record->monetized_searches}}</td>
                      <td>{{$record->ad_clicks}}</td>
                      <td>{{$record->date}}</td>
                      <td>{{$record->ctr}}</td>
                      <td>{{$record->cpc}}</td>
                      <td>{{$record->rpm}}</td>
                      <td>{{$record->revenue}}</td>
                      
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    <!-- Display pagination links -->
     {{ $data->appends($query_string)->links() }}
</div>



@endsection