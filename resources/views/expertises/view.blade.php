@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Category Details</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h4>{{ $category->name }}</h4>
                        <p><strong>Parent Category:</strong> {{ $category->parent ? $category->parent->name : 'None' }}</p>
                        <p><strong>Status:</strong> {!! $category->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</p>
                    </div>
                </div>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection 