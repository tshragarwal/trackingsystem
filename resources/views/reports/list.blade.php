@extends('layouts.app')

@section('content')

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<div class="container">
    <div class="card card-body" style="margin-bottom: 20px">
        <form action="/tracking/report/list" method='get'>
          <div class="row">
              
            <div class="col" id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="fa fa-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
                <input type="hidden" id="start_date" name="start_date" value="{{$query_string['start_date']??''}}">
                <input type="hidden" id="end_date" name="end_date" value="{{$query_string['end_date']??''}}">
            </div>
              
            <div class="col">
              <input type="text" class="form-control" name="subid" value="{{$query_string['subid']??''}}" placeholder="Enter Subid" aria-label="Subid">
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
                 @if( Auth::guard('web')->user()->user_type == "admin")
                    <a href="{{route('report.csv')}}" class="btn btn-primary"> {{ __('UPLOAD REPORT') }} </a>
                @endif
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
<script type="text/javascript">


$(function() {

    if (($.trim($('#start_date').val()) !== '' ) && ($.trim($('#end_date').val()) !== '' )){
         var start = moment($.trim($('#start_date').val()), "YYYY-MM-DD");
         var end = moment($.trim($('#end_date').val()), "YYYY-MM-DD");
         $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    } else{
        var start = moment();
        var end = moment();
    }
    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#start_date').val(start.format('YYYY-MM-DD'));
        $('#end_date').val(end.format('YYYY-MM-DD'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

});
</script>


@endsection