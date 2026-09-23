@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="col-sm-6 p-md-0 mt-2 mt-sm-0 d-flex">
            <a href="{{ route('web.faq.list') }}" class="btn btn-primary mb-1"><i class="fa fa-arrow-left"></i>
                {{ __('Back') }}</a>
        </div>

        <div class="row">
            <div class="col-xl-7 col-xxl-7 col-lg-7 m-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Edit') . ' ' . __('FAQ') }}</h4>
                    </div>
                    <div class="card-body">
                        @php
                            $content = json_decode($faq->content, true);
                            $content =
                                is_array($content) && count($content) > 0
                                    ? $content[0]
                                    : ['ques' => '', 'answer' => ''];
                        @endphp
                        <form action="{{ route('web.faq.update', $faq->slug) }}" method="POST">
                            @csrf
                            <div class="basic-form">
                                <div class="form-group">
                                    <label>Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $faq->faq_category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <label>{{ __('Question') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="question" class="form-control"
                                        placeholder="{{ __('Enter') }} {{ __('Question') }}"
                                        value="{{ old('question', $content['ques'] ?? '') }}" required>
                                    @error('question')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mt-3">
                                    <label>{{ __('Answer') }} <span class="text-danger">*</span></label>
                                    <textarea name="answer" class="form-control" placeholder="{{ __('Enter') }} {{ __('Answer') }}" rows="5"
                                        required>{{ old('answer', $content['answer'] ?? '') }}</textarea>
                                    @error('answer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mt-3">
                                    <label>Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="active" {{ $faq->status == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive" {{ $faq->status == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group text-right mt-3 mb-0">
                                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
