@extends('layouts.app')

@section('content')

<style>
    .table-responsive {
        max-width: 100%;
        overflow-x: auto;
    }
</style>
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="javascript:void(0)">Campaign List</a></li>
         
        </ol>
    </nav>
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
     <div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
           
              <th scope="col">Campaign Id</th>
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
                      <td>{{$record->advertiser->id}}</td>
                      <td>{{$record->advertiser->name}} ({{$record->advertiser->id}})</td>
                      <td>{{$record->campaign_name}}</td>
                      <td>{{$record->target_count}}</td>
                      <td>{{$record->target_url}}</td>
                      <td>{{ ($record->status == 1)? 'Active': (($record->status == 2)? 'Paused': 'Completed') }}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                      <td>
                          <a href="{{route('advertiser.detail', ['id' => $record->id])}}"><i class="fa fa-edit"></i></a>
                          @if($record->status == 1 || $record->status == 2)
                            <a style="margin-left:5px" href="{{route('publisher.job.form', ['campaign_id' => $record->id])}}"><i class="fa fa-tasks" aria-hidden="true"></a></i>
                          @endif
                          <a  style="margin-left: 5px;" href="javascript:void(0)" name="{{$record->campaign_name}}" data-toggle="modal" data-target="#deletecamp" class="delete_camp" id="{{$record->id}}" ><i  class="fa fa-trash-o "></i></a>
                      </td>
                      
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    </div>
    <!-- Display pagination links -->
   {{ $data->links() }}
</div>



<!-- Modal -->
<div class="modal fade" id="deletecamp" tabindex="-1" role="dialog" aria-labelledby="deletecampLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Delete Campaign</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body delete_body">
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger delete_camp_confirm" ad_id="">Delete</button>
      </div>
        <div class="delet_message">
        </div>
    </div>
  </div>
</div>


<script>
    $('.delete_camp').on('click', function(){
        $('.delete_camp_confirm').attr('ad_id', $(this).attr('id'));
        $('.delete_body').html('Do you want to delete <h5>' +$(this).attr('name') + '</h5>');
        
    });
    $('.delete_camp_confirm').on('click', function(){
        var campaign_id = $(this).attr('ad_id');
        $('.delete_message').html('');
    
        
        var token = $('meta[name="csrf-token"]').attr('content');
         var request = $.ajax({
            url: "/campaign/delete/",
            type: "POST",
            dataType: "json",
            data: {
                    _token: token, // Include the CSRF token
                    campaign_id: campaign_id // Include any other data you need for deletion
            },
            success: function(data){
                if(data.status == 0){
                    $('.delet_message').html('<div class="alert alert-danger" role="alert">'+ data.message+'</div>');
                     setTimeout(function () {
                        var closeButton = $('[data-dismiss="modal"]');
                                        closeButton.click();
                     }, 10000); // 10,000 milliseconds (10 seconds)
                }else{
                     $('.adver_'+advertizer_id).remove();
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