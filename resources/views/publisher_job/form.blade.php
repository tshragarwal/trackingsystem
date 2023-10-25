@extends('layouts.app')

@section('content')


<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Assign Publisher Job') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('publisher.job.save') }}">
                        @csrf

                            @if (session('success_status'))
                              <h6 class="alert alert-success">{{ session('success_status') }}</h6>
                              <h6 class="alert alert-primary">{{ session('link_url') }}</h6>
                           @endif
                           
                            @if (session('error_status'))
                              <h6 class="alert alert-danger">{{ session('error_status') }}</h6>
                           @endif
                           
                           
                        <div class="row mb-3">
                            <label for="advertiser_id" class="col-md-4 col-form-label text-md-end">{{ __('Select Advertiser') }}</label>

                            <div class="col-md-6">
                                <div class="form-group">
                                  
                                    <select class="form-control" name="advertiser_id" id="advertiser_id" >
                                        <option value="0">--SELECT--</option>
                                        @if(!empty($camp_array))
                                            <option value="{{$camp_array['advertiser_id']}}" selected >{{$camp_array['advertizer_name']}} ({{$camp_array['advertiser_id']}})</option>
                                        @else
                                            @foreach($advertiserObj as $object)
                                                <option value="{{$object->id}}">{{$object->name}} ({{$object->id}})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                @error('advertiser_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>  
                           
                        <div class="row mb-3">
                            <label for="advertiser_campaign_id" class="col-md-4 col-form-label text-md-end">{{ __('Select Campaign') }}</label>

                            <div class="col-md-6">
                                <div class="form-group">
                                    @if(!empty($camp_array))
                                        <select class="form-control advertiser_campaign_id_select" name="advertiser_campaign_id" id="advertiser_campaign_id" >
                                            <option value="{{$camp_array['campaign_id']}}" selected >{{$camp_array['campaign_name']}}</option>
                                        </select>
                                            
                                   @else
                                        <select class="form-control advertiser_campaign_id_select" name="advertiser_campaign_id" id="advertiser_campaign_id" ></select>
                                     @endif
                                    
                                </div>

                                @error('advertiser_campaign_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                           
                        <div id="form_further_info" style="{{ empty($camp_array)? 'display:none':'' }}">
                            <div class="row mb-3">
                                <label for="publisher_id" class="col-md-4 col-form-label text-md-end">{{ __('Select Publisher') }}</label>


                            <div class="col-md-6">
                                <div class="form-group">
                                  
                                    <select class="form-control" name="publisher_id" id="publisher_id" >
                                        <option value="0">--SELECT--</option>
                                        @foreach($publisher as $object)
                                            <option value="{{$object->id}}">{{$object->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @error('publisher_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            </div>

                            <div class="row mb-3">
                                <label for="target_count" class="col-md-4 col-form-label text-md-end">{{ __('Target Count') }}</label>

                                <div class="col-md-6">
                                    <input id="target_count" type="number" class="form-control @error('target_count') is-invalid @enderror" name="target_count" value="{{ old('target_count') }}" autocomplete="email">

                                    @error('target_count')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                   
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$('#advertiser_id').on('change', function(){
    $('#form_further_info').attr('style', 'display:none');
   var request = $.ajax({
        url: "/advertiser/campaign/list/"+ this.value,
        type: "GET",
        dataType: "json",
        success: function(data){
            html = '<option value="0">--SELECT--</option>';
            if ($.trim(data)){
                $.each(data, function(i) {
                    console.log(data[i].campaign_name);
                    
                    html += '<option value="'+data[i].id+'">'+data[i].campaign_name+'</option>';
                });
            $('#form_further_info').attr('style', 'display:block');    
            }else{
                $('#form_further_info').attr('style', 'display:none');
            }
            
            $('.advertiser_campaign_id_select').html(html);
            
        }
    });
});

</script>


@endsection