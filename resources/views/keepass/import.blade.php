@extends('layouts.app')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Home</a></li>
            <li class="breadcrumb-item">Keepass</li>
            <li class="breadcrumb-item active">Import</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="card border border-dark">
        <div class="card-header bg-dark text-center font-weight-bold">
            Import XML from KeePass
        </div>

        {{html()->form('POST', route('keepass.import'))->acceptsFiles()->class('needs-validation')->novalidate()->open()}}
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {{html()->label('Category name', 'category_name')}}
                        {{html()->text('category_name', old('category_name'))->required()->class('form-control')->placeholder('category name')}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group form-check">
                        {{html()->checkbox('with_icons', old('with_icons'))->class('form-check-input')}}
                        {{html()->label('Import icons (can duplicate existing icons if they exists in another category)', 'with_icons')->class('form-check-label')}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="import">Select XML to import</label>
                        {{html()->file('xml')->id('import')->accept('.xml')}}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            {{html()->submit('Import')->class('btn btn-success')}}
        </div>
        {{html()->form()->close()}}
    </div>
@endsection
