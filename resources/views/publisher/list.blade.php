@extends('layouts.app')

@section('content')
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link href="{{ asset('css/tablefixed.css') }}" rel="stylesheet">
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="javascript:void(0)">Publisher List</a></li>
         
        </ol>
    </nav>
     @if( !empty($success))
        <div class="alert alert-success" role="alert">
            New Advertiser data successfully saved.
        </div>
    @endif
    <a href="{{route('publisher.form')}}" class="btn btn-primary" style="float:right;">{{ __('Add New Publisher') }}</a>
    <div class="card card-body col-sm-7" style="margin-bottom: 20px">
       <form class="form-inline"  action="{{route('publisher.list')}}">
         <div class="form-group mx-sm-4">
           <label for="staticEmail2" class="sr-only"><lable> Filter List </lable></label>
           <select name="type" class="form-control">
               <option  value="0">-- Select --</option>
               <option {{!empty($filter) && !empty($filter['type']) && ($filter['type']=='id')?'selected':''}} value="id">Publisher Id</option>
               <option {{!empty($filter) && !empty($filter['type']) && ($filter['type']=='name')?'selected':''}} value="name">Publisher Name</option>
           </select>
         </div>
         <div class="form-group  mx-sm-5">
           <input type="text" class="form-control" name="v" value="{{!empty($filter['v'])?$filter['v']:''}}" placeholder="Enter Selected Type Value">
         </div>
         <button type="submit" class="btn btn-success mb-2">Filter</button>
       </form>
   </div>
     <div class="table-container">
    <div class="table-responsive">
    <table class="table table-hover" id='publisher_list_table'>
        <thead>
            <tr>
              <th data-field="id" data-sortable="true"  scope="col">Publisher Id <i class="fa fa-sort"></i></th>
              <th data-field="name" data-sortable="true"  scope="col">Publisher Name <i class="fa fa-sort"></i></th>
              <th data-field="email" data-sortable="true"  scope="col">Publisher Email <i class="fa fa-sort"></i></th>
              <th data-field="updated_at" data-sortable="true"  scope="col">Last Updated <i class="fa fa-sort"></i></th>
              <th data-field="created_at" data-sortable="true"  scope="col">Created At <i class="fa fa-sort"></i></th>
              <th scope="col">Action</th>

            </tr>
        </thead>
        <tbody>
            @if(!empty($data))
                @foreach($data as $record)
                  
                    <tr class="publisher_{{$record->id}}">
                      <th scope="row">{{$record->id}}</th>
                      <td>{{$record->name}}</td>
                      <td>{{$record->email}}</td>
                      <td>{{$record->updated_at}}</td>
                      <td>{{$record->created_at}}</td>
                      <td>
                          <a href="{{route('publisher.detail', ['id' => $record->id])}}"><i class="fa fa-edit"></i></a>
                          <a style='margin-left: 12px' href="{{route('publisher.job.list', ['publisher_id' => $record->id])}}"><i class="fa fa-eye"></i></a>
                          <a  style="margin-left: 12px;" href="javascript:void(0)" name="{{$record->name}}" data-toggle="modal" data-target="#deletecamp" class="delete_camp" id="{{$record->id}}" ><i  class="fa fa-trash-o "></i></a>
                      </td>
                    </tr>
                @endforeach
            @endif

        </tbody>
    </table>
    </div>
    </div>
    <br/>
    <!-- Display pagination links -->
       {{ $data->links() }}
       
    <br/>
    <br/>
    <br/>
 
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


<script>
    $(document).ready(function() {
        $('#publisher_list_table').bootstrapTable();
        $('.fixed-table-loading').css('display', 'none');
    });
</script>

@endsection