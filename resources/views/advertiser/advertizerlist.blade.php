@extends('layouts.app')

@section('content')

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
            New Advertizer data successfully saved.
        </div>
    @endif
    <table class="table table-hover">
        <thead>
            <tr>
              <th scope="col">Advertizer Id</th>
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
                  
                    <tr class="adver_{{$record->id}}">
                      <th scope="row">{{$record->id}}</th>
                      <td>{{$record->name}}</td>
                      <td>{{$record->manual_email}}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                      <td>
                          <a href="{{route('advertiser.form', ['adv_id' => $record->id])}}"><i class="fa fa-edit"> </i></a>
                          <a style="margin-left: 12px;" href="{{route('campaign.list', ['advertizer' => $record->id])}}"><i class="fa fa-eye"> </i></a>
                          <a  style="margin-left: 12px;" href="javascript:void(0)" name="{{$record->name}}" data-toggle="modal" data-target="#deleteAdvertizer" class="delete_advertizer" id="{{$record->id}}" ><i  class="fa fa-trash-o "></i></a>
                      </td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
       {{ $data->links() }}
 
</div>


<!-- Modal -->
<div class="modal fade" id="deleteAdvertizer" tabindex="-1" role="dialog" aria-labelledby="deleteAdvertizerLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Delete Advertizer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body delete_body">
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger delete_advertizer_confirm" ad_id="">Delete</button>
      </div>
        <div class="delet_message">
        </div>
    </div>
  </div>
</div>


<script>
    $('.delete_advertizer').on('click', function(){
        $('.delete_advertizer_confirm').attr('ad_id', $(this).attr('id'));
        $('.delete_body').html('Deleting the record will not be reverted. Do you want to delete <h5>' +$(this).attr('name') + '</h5>');
        
    });
    $('.delete_advertizer_confirm').on('click', function(){
        var advertizer_id = $(this).attr('ad_id');
        $('.delete_message').html('');
    
        
        var token = $('meta[name="csrf-token"]').attr('content');
         var request = $.ajax({
            url: "/advertiser/delete/",
            type: "POST",
            dataType: "json",
            data: {
                    _token: token, // Include the CSRF token
                    advertizer_id: advertizer_id // Include any other data you need for deletion
            },
            success: function(data){
                if(data.status == 0){
                    $('.delet_message').html('<div class="alert alert-danger" role="alert">'+ data.message+'</div>');
                     setTimeout(function () {
                        var closeButton = $('[data-dismiss="modal"]');
                                        closeButton.click();
                     }, 15000); // 10,000 milliseconds (10 seconds)
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