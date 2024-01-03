@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<link href="{{ asset('css/tablefixed.css') }}" rel="stylesheet">
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Traffic Report</a></li>
      <li class="breadcrumb-item"><a href="javascript:void(0)">Keyword</a></li>
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
            <div class="col-md-12 col-lg-4">
              <lable>Advertiser</lable>
              <select id="advertizer_select"  class="selectpicker form-control" multiple data-live-search="true">
                 @foreach($publisher_advertizer_list['advertizer_list'] as $advert)    
                <option value="{{$advert['id']}}">{{$advert['name']}}</option>
                  @endforeach    
              </select>
            </div>
            <input type="hidden" id="advertiser_id" name="advertiser_id" value="">
        @endif
        
        <div class="col-md-12 col-lg-4">
          <lable> Offer Id</lable>
          <input type="text" class="form-control" name="offer_id" value="{{$query_string['offer_id']??''}}" placeholder="" aria-label="offer_id">
        </div>
        
        <div class="col-md-12 col-lg-4">
          <lable> SubId</lable>
          <input type="text" class="form-control" name="subid" value="{{$query_string['subid']??''}}" placeholder="" aria-label="subid">
        </div>
        
        <div class="col-md-12 col-lg-4" style="margin-top: 22px">
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </div>
    </form>
  </div>

    <div class="table-container">  
  <div class="table-responsive table-cont">
    <table class="table table-hover" id='tracking_url_keyword_table'>
      <thead>
        <tr> 
        
          <th data-field="offr_id" data-sortable="true" scope="col">Offer Id <i class="fa fa-sort"></i></th>
          <th data-field="advertiser_name" data-sortable="true" scope="col">Advertiser Name <i class="fa fa-sort"></i></th>
          <th data-field="campaign_name" data-sortable="true" scope="col">Campaign Name <i class="fa fa-sort"></i></th>
          <th data-field="pub_name" data-sortable="true" scope="col">Publisher Name <i class="fa fa-sort"></i></th>
          <th data-field="subid" data-sortable="true" scope="col">Advertiser Subid <i class="fa fa-sort"></i></th>
          <th data-field="keyword" data-sortable="true" scope="col">Keyword <i class="fa fa-sort"></i></th>
          <th data-field="count" data-sortable="true" scope="col">Count <i class="fa fa-sort"></i></th>
         
       </tr>
      </thead>
      <tbody>
          @if(!empty($data))
            @foreach($data as $record)
                <tr>
                 <td scope="row">{{$record->publisher_job_id}}  </td>
                 <td scope="row"> {{$record->advertiser->name}} </td>
                 <td scope="row"> {{$record->campaign->campaign_name}} </td>
                 <td scope="row"> {{$record->publisher->name}} </td>
                 <td scope="row"> {{$record->subid}} </td>
                <td scope="row"> {{$record->keyword}} </td>
                 <td scope="row"> {{$record->total_count}}  </td>
                </tr>
            @endforeach
          @endif
      </tbody>
      
    </table>
  </div>
  </div>
  <br/>
    
  <!-- Display pagination links --> 
  {{ $data->appends($query_string)->links() }} 
  <br/>
  <br/>
</div>
  



<script type="text/javascript">
$(function() {
    
      $(document).ready(function() {
        $('#tracking_url_keyword_table').bootstrapTable();
        $('.fixed-table-loading').css('display', 'none');
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

$('#publisher_select').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    var selectedValues = $('#publisher_select').val();
    $('#publishers_id').val(selectedValues);
});
$('#advertizer_select').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
    var selectedValues = $('#advertizer_select').val();
    $('#advertiser_id').val(selectedValues);
});


    
</script>



@endsection