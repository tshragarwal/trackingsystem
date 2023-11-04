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
    
    
    <div class="card">
        <div class="card-header">
          Publisher Report API
        </div>
        <div class="card-body">
          <h6 class="card-title">{{$domain}}/publisher/token/data?token={{$user->api_token}}&start_date=2023-10-10&end_date=2023-10-10&report_type=n2s&format=json</h6>
          
          <br/>
          <br/>
         
          <p class="card-text">
               Required parameter start with *
               <br/><br/>
               
              <b>*token</b> : This parameter is a fixed value included in the URL<br/>
              <b>*start_date</b> : This parameter filters data based on the start date. Date format will be Year-Month-date (<b>2023-10-18</b>) <br/>
              <b>end_date</b> : This parameter filters data based on the end date. Date format will be Year-Month-date (<b>2023-10-18</b>) <br/>
              <b>*report_type</b> : Use this parameter to filter data by report type. Only two report formats are accepted, which are <b>n2s</b> / <b>typein</b>. <br/>
              <b>*format</b> : This parameter allows you to specify the desired response format, such as <b>json</b> or <b>csv</b>. Only two formats are accepted: json / csv. <br/>
          </p>
        </div>
    </div>
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