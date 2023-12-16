@extends('layouts.app')
@section('content') 
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<div class="container">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="javascript:void(0)">Publisher Job List</a></li>
    </ol>
  </nav>
  @if($user_type == 'admin') <a href="{{route('publisher.job.form')}}" class="btn btn-primary" style="margin-bottom:20px;float:right;">{{ __('Assign Publisher Job') }}</a> @endif
  @if( !empty($success))
  <div class="alert alert-success" role="alert"> New Advertiser data successfully saved. </div>
  @endif
  
  
    <div class="card card-body col-sm-7" style="margin-bottom: 20px">
       <form class="form-inline"  action="{{route('publisher.job.list')}}">
         <div class="form-group mx-sm-4">
           <label for="staticEmail2" class="sr-only"><lable> Filter List </lable></label>
           <select name="type" class="form-control">
               <option  value="0">-- Select --</option>
               <option {{!empty($filter) && !empty($filter['type']=='id')?'selected':''}} value="id">Job ID</option>
               <option {{!empty($filter) && !empty($filter['type']=='pub_name')?'selected':''}} value="pub_name">Publisher Name</option>
               <option {{!empty($filter) && !empty($filter['type']=='adver_name')?'selected':''}} value="adver_name">Advertiser Name</option>
               <option {{!empty($filter) && !empty($filter['type']=='campaign_name')?'selected':''}} value="campaign_name">Campaign Name</option>
           </select>
         </div>
         <div class="form-group  mx-sm-5">
           <input type="text" class="form-control" name="v" value="{{!empty($filter['v'])?$filter['v']:''}}" placeholder="Enter Selected Type Value">
         </div>
         <button type="submit" class="btn btn-success mb-2">Filter</button>
       </form>
   </div>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">Job Id</th>
          <th scope="col">Publisher Name</th>
          <th scope="col">Advertizer name</th>
          <th scope="col">Campaign name</th>
          <th scope="col">Campaign Target Url</th>
          <th scope="col">Link</th>
          <th scope="col">Target Count</th>
          <th scope="col">Tracking Count</th>
          <th scope="col">Updated At</th>
          <th scope="col">Created At</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
      
      @if(!empty($data))
      @foreach($data as $record)
      <tr class="publisher_job_id_{{$record->id}}">
        <th scope="row">{{$record->id}}</th>
        <td>{{$record->publisher->name}} ({{$record->publisher->id}})</td>
        <td>{{$record->campaign->advertiser->name}} ({{$record->campaign->advertiser->id}})</td>
        <td>{{$record->campaign->campaign_name}}</td>
        <td>{{$record->campaign->target_url}}</td>
        <td>{{$domain}}/search?code={{$record->proxy_url}}&offerid={{$record->id}}&q={keyword}</td>
        <td>{{$record->target_count}}</td>
        <td>{{$record->tracking_count}}</td>
        <td>{{$record->updated_at}}</td>
        <td>{{$record->created_at}}</td>
        <td><a href="javascript:void(0)" data-toggle="modal" data-target="#deletecamp" class="delete_camp" id="{{$record->id}}" ><i  class="fa fa-trash-o "></i></a></td>
      </tr>
      @endforeach
      @endif
      </tbody>
      
    </table>
  </div>
  <!-- Display pagination links --> 
  {{ $data->links() }} </div>
<!-- Modal -->
<div class="modal fade" id="deletecamp" tabindex="-1" role="dialog" aria-labelledby="deletecampLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Publisher Job</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
      </div>
      <div class="modal-body delete_body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger delete_camp_confirm" ad_id="">Delete</button>
      </div>
      <div class="delet_message"></div>
    </div>
  </div>
</div>
<script>
    $('.delete_camp').on('click', function(){
        $('.delete_camp_confirm').attr('ad_id', $(this).attr('id'));
        $('.delete_body').html('Deleting the Joblisher Job will not be reverted.'); 
    });
    $('.delete_camp_confirm').on('click', function(){
        var publisher_job_id = $(this).attr('ad_id');
        $('.delete_message').html('');
        var token = $('meta[name="csrf-token"]').attr('content');
         var request = $.ajax({
            url: "/publisher/job/delete",
            type: "POST",
            dataType: "json",
            data: {
                    _token: token, // Include the CSRF token
                    publisher_job_id: publisher_job_id // Include any other data you need for deletion
            },
            success: function(data){
                if(data.status == 0){
                    $('.delet_message').html('<div class="alert alert-danger" role="alert">'+ data.message+'</div>');
                     setTimeout(function () {
                        var closeButton = $('[data-dismiss="modal"]');
                                        closeButton.click();
                     }, 10000); // 10,000 milliseconds (10 seconds)
                }else{
                     $('.publisher_job_id_'+publisher_job_id).remove();
                     $('.delet_message').html('<div class="alert alert-primary" role="alert">'+ data.message+'</div>');
                      setTimeout(function () {
                        var closeButton = $('[data-dismiss="modal"]');
                                        closeButton.click();
                     }, 5000); // 10,000 milliseconds (10 seconds)
                }
            }
        });
    });
</script> 
@endsection