@extends('layouts.app')

@section('content')
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="javascript:void(0)">Publisher Api Token</a></li>
         
        </ol>
    </nav>
    @if( Auth::guard('web')->user()->user_type == "publisher")
        <div class="row " style="margin-right: 40px;margin-bottom: 20px;">
            <div class=" offset-md-10">
                <form method="POST" action="{{ route('publisher_token.token_generate') }}">
                    @csrf
                    <button type="submit"  class="btn btn-primary"> {{ __('Generate Publisher Token') }} </button>

                </form>
            </div>
        </div>
    @endif
    
    @if (session('success_status'))
        <h6 class="alert alert-success">{{ session('success_status') }}</h6>
    @endif
    
    @if(!empty($user))
        <table class="table table-hover">
            <thead>
                <tr>
                  <th scope="col">Publisher Id</th>
                  <th scope="col">Api Token</th>
                </tr>
            </thead>
            <tbody>
                <tr class="publisher_{{$user->id}}">
                  <td scope="row">{{$user->id}}</td>
                  <td>{{$user->api_token}}</td>

                </tr>


            </tbody>
        </table>
    @endif
 
</div>



<!-- Modal -->
<div class="modal fade" id="deletecamp" tabindex="-1" role="dialog" aria-labelledby="deletecampLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" >Delete Publisher</h5>
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
        $('.delete_body').html('Deleting the record will not be reverted. Do you want to delete <h5>' +$(this).attr('name') + '</h5>');
        
    });
    $('.delete_camp_confirm').on('click', function(){
        var publisher_id = $(this).attr('ad_id');
        $('.delete_message').html('');
    
        
        var token = $('meta[name="csrf-token"]').attr('content');
         var request = $.ajax({
            url: "/publisher/delete/",
            type: "POST",
            dataType: "json",
            data: {
                    _token: token, // Include the CSRF token
                    publisher_id: publisher_id // Include any other data you need for deletion
            },
            success: function(data){
                if(data.status == 0){
                    $('.delet_message').html('<div class="alert alert-danger" role="alert">'+ data.message+'</div>');
                     setTimeout(function () {
                        var closeButton = $('[data-dismiss="modal"]');
                                        closeButton.click();
                     }, 10000); // 10,000 milliseconds (10 seconds)
                }else{
                     $('.publisher_'+publisher_id).remove();
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