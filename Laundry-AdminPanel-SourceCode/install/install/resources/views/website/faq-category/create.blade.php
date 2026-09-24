@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
            <a href="{{ route('web.faq.category.index') }}" class="btn btn-primary mb-1"><i class="fa fa-arrow-left"></i>
                Back</a>
        </div>

        <div class="row">
            <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add FAQ Category</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('web.faq.category.store') }}" method="POST">
                            @csrf
                            <div class="basic-form">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mt-3">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group text-right mt-3 mb-0">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
