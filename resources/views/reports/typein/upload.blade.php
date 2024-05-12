@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Report</a></li>
                <li class="breadcrumb-item"><a href="{{ route('report.list', ['company_id' => $companyID, 'type' => 'typein']) }}">Typein
                        Report</a></li>
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



        <div style="margin-left:80%">
            <form action="{{ route('report.downloadSample', ['company_id' => $companyID, 'type' => 'typein']) }}">
                @csrf
                <button type="submit" class="btn btn-success">Download Sample CSV</button>
            </form>
        </div>
        <form action="{{ route('report.saveCSV', ['company_id' => $companyID, 'type' => 'typeins']) }}" method="POST"
            enctype="multipart/form-data" style="width: 50%">
            @csrf
            <div class="mb-3">
                <label for="formFile" class="form-label">Choose CSV file</label>
                <input class="form-control" type="file" id="formFile" name="csv_file">
            </div>
            
            <button type="submit" class="btn btn-primary">Upload CSV</button>
        </form>

    </div>

@endsection
