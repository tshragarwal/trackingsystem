@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Report</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report.list', ['company_id' => $companyID, 'type' => 'n2s']) }}">N2S Report</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Upload Report</a></li>
            </ol>
        </nav>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="container">
            <div class="col-sm-12">
                <div class="" style="margin-left:80%">
                    <form action="{{ route('report.downloadSample', ['company_id' => $companyID, 'type' => 'n2s']) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Download Sample CSV</button>
                    </form>
                </div>

                <div class="">
                    <form action="{{ route('report.saveCSV', ['company_id' => $companyID, 'type' => 'n2s']) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Choose CSV file</label>
                            <input class="form-control" type="file" id="formFile" name="csv_file">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Upload CSV</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection
