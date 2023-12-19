@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<link href="{{ asset('css/tablefixed.css') }}" rel="stylesheet">
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Report</a></li>
      <li class="breadcrumb-item"><a href="javascript:void(0)">Typein Report</a></li>
    </ol>
  </nav>
  <div class="card card-body" style="margin-bottom: 20px">
    <form action="{{route('report.typein_list')}}" method='get'>
      <div class="row">
        <div class="col-md-12 col-lg-4">
          <lable> Date Range</lable>
          <div id="reportrange" class="form-control"> <span></span> <i class="fa fa-calendar"></i>
            <input type="hidden" id="start_date" name="start_date" value="{{$query_string['start_date']??''}}">
            <input type="hidden" id="end_date" name="end_date" value="{{$query_string['end_date']??''}}">
          </div>
        </div>
        <div class="col-md-12 col-lg-4">
          <lable>Enter Subid</lable>
          <input type="text" class="form-control" name="subid" value="{{$query_string['subid']??''}}" placeholder="" aria-label="Subid">
        </div>
        @if($adminFlag == true)
        <div class="col-md-12 col-lg-4">
          <lable> Country Name</lable>
          <input type="text" class="form-control" name="country" value="{{$query_string['country']??''}}" placeholder="" aria-label="country">
        </div>
        @endif 
        
        @if(!empty($publisher_advertizer_list) && !empty($publisher_advertizer_list['publisher_list']))
        <div class="col-md-12 col-lg-4">
          <lable> Publisher </lable>
          <select id="publisher_select"  class="form-control selectpicker" multiple data-live-search="true">
                @foreach($publisher_advertizer_list['publisher_list'] as $publisher)    
            <option value="{{$publisher['id']}}">{{$publisher['name']}}</option>
                @endforeach
          </select>
        </div>
        <input type="hidden" id="publishers_id" name="publishers_id" value="">
        @endif
        
        @if(!empty($publisher_advertizer_list) && !empty($publisher_advertizer_list['advertizer_list']))
        <div class="col-sm-4">
          <lable> Advertizer </lable>
          <select id="advertizer_select" class="selectpicker form-control" multiple data-live-search="true">
                @foreach($publisher_advertizer_list['advertizer_list'] as $advert)
            <option value="{{$advert['name']}}">{{$advert['name']}}</option>
                @endforeach
          </select>
        </div>
        <input type="hidden" id="advertizers_name" name="advertizers_name" value="{{$query_string['end_date']??''}}">
        @endif
        <div class="col" style="margin-top: 22px">
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </div>
    </form>
  </div>
  <div style="margin-bottom: 20px;text-align: end;">
    <form action="{{route('report.typein_downloadcsv')}}" method='get'>
      <div class="row">
        <div class="col">
          <input type='hidden' name='query_string' value="{{http_build_query($query_string)}}" />
          <button target="_blank" type="submit" class="btn btn-success">DOWNLOAD CSV</button>
          @if($adminFlag == true) <a href="{{route('report.typein_csv')}}" class="btn btn-primary"> {{ __('UPLOAD REPORT') }} </a> @endif </div>
      </div>
    </form>
  </div>
    
  <div class="table-container">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr> @if($adminFlag == true)
          <th scope="col">#</th>
          <th scope="col">Date</th>
          <th scope="col">Advertizer Name</th>
          <th scope="col">Campaign Name</th>
          <th scope="col">Campaign id</th>
          <th scope="col">Advertizer Subid</th>
          <th scope="col">Total Searches</th>
          <th scope="col">Monetized Searches</th>
          <th scope="col">Ad Clicks</th>
          <th scope="col">Ad Coverage</th>
          <th scope="col">CTR</th>
          <th scope="col">CPC ($)</th>
          <th scope="col">RPM ($)</th>
          <th scope="col">Gross Revenue ($)</th>
          <th scope="col">Publisher Name</th>
          <th scope="col">Publisher Id</th>
          <th scope="col">Offer Id</th>
          <th scope="col">Publisher RPM ($)</th>
          <th scope="col">Publisher RPC ($)</th>
          <th scope="col">Net Revenue ($)</th>
          <th scope="col">Country</th>
          @else
          <th scope="col">Date</th>
          <th scope="col">Offer Id</th>
          <th scope="col">Country</th>
          <th scope="col">Total Searches</th>
          <th scope="col">Monetized Searches</th>
          <th scope="col">Ad Clicks</th>
          <th scope="col">Ad Coverage</th>
          <th scope="col">CTR</th>
          <th scope="col"> RPC ($)</th>
          <th scope="col"> RPM ($)</th>
          <th scope="col">Net Revenue ($)</th>
          @endif </tr>
      </thead>
      <tbody>
      
      @if(!empty($data))
      @foreach($data as $record)
      <tr> @if($adminFlag == true)
        <th scope="row">{{$record->id}}</th>
        <td scope="row">{{$record->date}}</td>
        <td scope="row">{{$record->advertiser_name}}</td>
        <td scope="row">{{$record->campaign_name}}</td>
        <td scope="row">{{$record->campaign_id}}</td>
        <td scope="row">{{$record->subid}}</td>
        <td scope="row">{{$record->total_searches}}</td>
        <td scope="row">{{$record->monetized_searches}}</td>
        <td scope="row">{{$record->ad_clicks}}</td>
        <td scope="row">{{$record->ad_coverage}}</td>
        <td scope="row">{{$record->ctr}}</td>
        <td scope="row">$ {{$record->cpc}}</td>
        <td scope="row">$ {{$record->rpm}}</td>
        <td scope="row">$ {{$record->gross_revenue}}</td>
        <td scope="row">{{$record->publisher_name}}</td>
        <td scope="row">{{$record->publisher_id}}</td>
        <td scope="row">{{$record->offer_id}}</td>
        <td scope="row">$ {{$record->publisher_RPM}}</td>
        <td scope="row">$ {{$record->publisher_RPC}}</td>
        <td scope="row">$ {{$record->net_revenue}}</td>
        <td scope="row">{{$record->country}}</td>
        @else
        <td scope="row">{{$record->date}}</td>
        <td scope="row">{{$record->offer_id}}</td>
        <td scope="row">{{$record->country}}</td>
        <td scope="row">{{$record->total_searches}}</td>
        <td scope="row">{{$record->monetized_searches}}</td>
        <td scope="row">{{$record->ad_clicks}}</td>
        <td scope="row">{{$record->ad_coverage}}</td>
        <td scope="row">{{$record->ctr}}</td>
        <td scope="row">$ {{$record->publisher_RPC}}</td>
        <td scope="row">$ {{$record->publisher_RPM}}</td>
        <td scope="row">$ {{$record->net_revenue}}</td>
        @endif </tr>
      @endforeach
      @endif
      </tbody>
      
    </table>
  </div>
  </div>
    <br/>
  <!-- Display pagination links --> 
  {{ $data->appends($query_string)->links() }} 
<br/><br/><br/>
</div>
<script type="text/javascript">
$(function() {
$('#publisher_select').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    var selectedValues = $('#publisher_select').val();
    $('#publishers_id').val(selectedValues);
});
$('#advertizer_select').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    var selectedValues = $('#advertizer_select').val();
    $('#advertizers_name').val(selectedValues);
});

    if (($.trim($('#start_date').val()) !== '' ) && ($.trim($('#end_date').val()) !== '' )){
         var start = moment($.trim($('#start_date').val()), "YYYY-MM-DD");
         var end = moment($.trim($('#end_date').val()), "YYYY-MM-DD");
         $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    } else{
        var start = 0;
        var end = 0;
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