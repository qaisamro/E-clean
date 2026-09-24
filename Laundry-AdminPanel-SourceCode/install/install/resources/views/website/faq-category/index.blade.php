@extends('layouts.app')

@section('title', 'FAQ Categories')

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">FAQ Categories</h4>
                        <a href="{{ route('web.faq.category.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Category
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="myTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td>{{ $category->id }}</td>
                                                <td>{{ $category->name }}</td>
                                                <td>
                                                    <label class="switch">
                                                        @role('visitor')
                                                            <a class="visitorMessage">
                                                            @else
                                                                <a
                                                                    href="{{ route('web.faq.category.status.toggle', $category->id) }}">
                                                                @endrole
                                                                <input type="checkbox"
                                                                    {{ $category->status == 'active' ? 'checked' : '' }}>
                                                                <span class="slider round"></span>
                                                            </a>
                                                    </label>
                                                </td>
                                                <td>
                                                    <a href="{{ route('web.faq.category.edit', $category->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('web.faq.category.delete', $category->id) }}"
                                                        class="btn btn-sm btn-danger delete-confirm">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
