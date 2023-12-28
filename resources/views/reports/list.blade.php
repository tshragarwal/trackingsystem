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
      <li class="breadcrumb-item"><a href="#">Report</a></li>
      <li class="breadcrumb-item"><a href="javascript:void(0)">N2S Report</a></li>
    </ol>
  </nav>
    @if (session('success_status'))
      <h6 class="alert alert-success">{{ session('success_status') }}</h6>
   @endif
    @if (session('error_status'))
      <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
   @endif
  <div class="card card-body" style="margin-bottom: 20px">
    <form action="{{route('report.list')}}" method='get'>
      <div class="row">
        <div class="col-md-12 col-lg-4">
          <lable> Date Range</lable>
          <div id="reportrange" class="form-control"> <span></span> <i class="fa fa-calendar"></i>
            <input type="hidden" id="start_date" name="start_date" value="{{$query_string['start_date']??''}}">
            <input type="hidden" id="end_date" name="end_date" value="{{$query_string['end_date']??''}}">
          </div>
        </div>
        <div class="col-md-12 col-lg-4">
          <lable> Enter Subid</lable>
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
        <div class="col-md-12 col-lg-4">
          <lable>Advertiser</lable>
          <select id="advertizer_select"  class=" selectpicker form-control" multiple data-live-search="true">
             @foreach($publisher_advertizer_list['advertizer_list'] as $advert)    
            <option value="{{$advert['name']}}">{{$advert['name']}}</option>
              @endforeach    
          </select>
        </div>
        <input type="hidden" id="advertizers_name" name="advertizers_name" value="{{$query_string['end_date']??''}}">
        @endif
        <div class="col-md-12 col-lg-4" style="margin-top: 22px">
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </div>
    </form>
  </div>
  <div style="margin-bottom: 20px;text-align: end;">
    <form action="{{route('report.downloadcsv')}}" method='get'>
      <div class="row">
        <div class="col">
          <input type='hidden' name='query_string' value="{{http_build_query($query_string)}}" />
          <button target="_blank" type="submit" class="btn btn-success">DOWNLOAD CSV</button>
          @if( Auth::guard('web')->user()->user_type == "admin") <a href="{{route('report.csv')}}" class="btn btn-primary"> {{ __('UPLOAD REPORT') }} </a> <a href="#"   data-toggle="modal" data-target="#deletAllReport" class="btn btn-danger delete_report"> {{ __('DELETE REPORT') }} </a> @endif </div>
      </div>
    </form>
  </div>
    <div class="table-container">  
  <div class="table-responsive table-cont">
    <table class="table table-hover" id='n2s_report_list_table'>
      <thead>
        <tr> @if($adminFlag == true)
          <th scope="col">#</th>
          <th data-field="date" data-sortable="true" scope="col">Date <i class="fa fa-sort"></i></th>
          <th data-field="advertiser_name" data-sortable="true" scope="col">Advertiser Name <i class="fa fa-sort"></i></th>
          <th data-field="campaign_name" data-sortable="true" scope="col">Campaign Name <i class="fa fa-sort"></i></th>
          <th data-field="campaign_id" data-sortable="true" scope="col">Campaign id <i class="fa fa-sort"></i></th>
          <th data-field="subid" data-sortable="true" scope="col">Advertiser Subid <i class="fa fa-sort"></i></th>
          <th data-field="searches" data-sortable="true" scope="col">Searches <i class="fa fa-sort"></i></th>
          <th data-field="clicks" data-sortable="true" scope="col">Clicks <i class="fa fa-sort"></i></th>
          <th data-field="tq" data-sortable="true" scope="col">TQ <i class="fa fa-sort"></i></th>
          <th data-field="ctr" data-sortable="true" scope="col">CTR (in %) <i class="fa fa-sort"></i></th>
          <th data-field="adv_cpc" data-sortable="true" scope="col">Advertiser CPC ($) <i class="fa fa-sort"></i></th>
          <th data-field="adv_rpm" data-sortable="true" scope="col">Advertiser RPM ($) <i class="fa fa-sort"></i></th>
          <th data-field="gross_revenue" data-sortable="true" scope="col">Gross Revenue ($) <i class="fa fa-sort"></i></th>
          <th data-field="pub_name" data-sortable="true" scope="col">Publisher Name <i class="fa fa-sort"></i></th>
          <th data-field="pub_id" data-sortable="true" scope="col">Publisher Id <i class="fa fa-sort"></i></th>
          <th data-field="offer_id" data-sortable="true" scope="col">Offer Id <i class="fa fa-sort"></i></th>
          <th data-field="pub_rpm" data-sortable="true" scope="col">Publisher RPM ($) <i class="fa fa-sort"></i></th>
          <th data-field="pub_rpc" data-sortable="true" scope="col">Publisher RPC ($) <i class="fa fa-sort"></i></th>
          <th data-field="net_revenue" data-sortable="true" scope="col">Net Revenue ($) <i class="fa fa-sort"></i></th>
          <th data-field="country" data-sortable="true" scope="col">Country <i class="fa fa-sort"></i></th>
          <th data-field="id" data-sortable="true" scope="col">Action</th>
          @else
          <th data-field="date" data-sortable="true" scope="col">Date <i class="fa fa-sort"></i></th>
          <th data-field="p_offer_id" data-sortable="true" scope="col">Offer Id <i class="fa fa-sort"></i></th>
          <th data-field="p_country" data-sortable="true" scope="col">Country <i class="fa fa-sort"></i></th>
          <th data-field="p_total_search" data-sortable="true" scope="col">Total Searches <i class="fa fa-sort"></i></th>
          <th data-field="p_add_clicks" data-sortable="true" scope="col">Ad Clicks <i class="fa fa-sort"></i></th>
          <th data-field="p_ctr" data-sortable="true" scope="col">CTR (in %) <i class="fa fa-sort"></i></th>
          <th data-field="p_rpc" data-sortable="true" scope="col">RPC ($) <i class="fa fa-sort"></i></th>
          <th data-field="p_rpm" data-sortable="true" scope="col">RPM ($) <i class="fa fa-sort"></i></th>
          <th data-field="p_net_revenue" data-sortable="true" scope="col">Net Revenue ($) <i class="fa fa-sort"></i></th>
          <th data-field="p_tq" data-sortable="true" scope="col">TQ <i class="fa fa-sort"></i></th>
          @endif </tr>
      </thead>
      <tbody>
      
      @if(!empty($data))
      @foreach($data as $record)
      <tr class="n2s_{{$record->id}}"> @if($adminFlag == true)
        <th scope="row">{{$record->id}}</th>
        <td scope="row">{{$record->date}}</td>
        <td scope="row">{{$record->advertiser_name}}</td>
        <td scope="row">{{$record->campaign_name}}</td>
        <td scope="row">{{$record->campaign_id}}</td>
        <td scope="row">{{$record->subid}}</td>
        <td scope="row">{{$record->total_searches}}</td>
        <td scope="row">{{$record->ad_clicks}}</td>
        <td scope="row">{{$record->tq}}</td>
        <td scope="row">{{$record->ctr}}</td>
        <td scope="row">$ {{$record->advertiser_CPC}}</td>
        <td scope="row">$ {{$record->advertiser_RPM}}</td>
        <td scope="row">$ {{$record->gross_revenue}}</td>
        <td scope="row">{{$record->publisher_name}}</td>
        <td scope="row">{{$record->publisher_id}}</td>
        <td scope="row">{{$record->offer_id}}</td>
        <td scope="row">$ {{$record->publisher_RPM}}</td>
        <td scope="row">$ {{$record->publisher_RPC}}</td>
        <td scope="row">$ {{$record->revenue}}</td>
        <td scope="row">{{$record->country}}</td>
        <td scope="row">
            <a href="{{route('report.n2s_report_edit', ['id' => $record->id])}}" ><i  class="fa fa-edit "></i></a>
            <a  style="margin-left: 12px;" href="javascript:void(0)" name="{{$record->id}}" data-toggle="modal" data-target="#deletecamp" class="delete_report" id="{{$record->id}}" ><i  class="fa fa-trash-o "></i></a>
        </td>
        @else
        <td scope="row">{{$record->date}}</td>
        <td scope="row">{{$record->offer_id}}</td>
        <td scope="row">{{$record->country}}</td>
        <td scope="row">{{$record->total_searches}}</td>
        <td scope="row">{{$record->ad_clicks}}</td>
        <td scope="row">{{$record->ctr}}</td>
        <td scope="row">$ {{$record->publisher_RPC}}</td>
        <td scope="row">$ {{$record->publisher_RPM}}</td>
        <td scope="row">$ {{$record->revenue}}</td>
        <td scope="row">{{$record->tq}}</td>
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
  <br/>
  <br/>
</div>
  



<!-- Modal -->
<div class="modal fade" id="deletecamp" tabindex="-1" role="dialog" aria-labelledby="deletecampLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Delete N2S Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body delete_body">
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger delete_report_confirm" ad_id="">Delete</button>
      </div>
        <div class="delet_message">
        </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="deletAllReport" tabindex="-1" role="dialog" aria-labelledby="deletecampLabel1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" >Delete All N2S Report</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <b>It will Delete All N2S Report Data and Deleted report will not be reverted. Do you want to delete</b>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger delete_all_report_confirm" ad_id="">Delete</button>
      </div>
        <div class="delet_message_all">
        </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function() {

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
    $('#advertizers_name').val(selectedValues);
});



    $('.delete_all_report_confirm').on('click', function(){
        var token = $('meta[name="csrf-token"]').attr('content');
         var request = $.ajax({
            url: '/report/n2s/all/delete',
            type: "POST",
            dataType: "json",
            data: { _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(data){
                $('.delet_message_all').html('<div class="alert alert-success" role="alert">Data Deleted</div>');
                      setTimeout(function () {
                       location.reload();
                     }, 2000);
            }
        });
    });
    
</script>


<script>
    $(document).ready(function() {
        $('#n2s_report_list_table').bootstrapTable();
        $('.fixed-table-loading').css('display', 'none');
    });
</script>
@endsection