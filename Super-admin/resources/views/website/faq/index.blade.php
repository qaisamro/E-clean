@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 d-flex align-items-center justify-content-between">
                        <h2 class="card-title m-0">{{ __('All') . ' ' . __('FAQs') }}</h2>
                        <div class="d-flex justify-content-end">
                            <form class="" action="{{ route('web.faq.list') }}" method="GET">
                                <div class="d-flex align-items-center gap-2">
                                    <select name="category_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('web.faq.create') }}" class="btn btn-primary">
                                        {{ __('Add_New') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body pt-2">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped {{ session()->get('local') }}" id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('SL') }}</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">{{ __('Question') }}</th>
                                        <th scope="col">{{ __('Answer') }}</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($faqs as $index => $faq)
                                        @php
                                            $items = json_decode($faq->content, true);
                                            $items = is_array($items) ? $items : [];
                                            $item = count($items) > 0 ? $items[0] : ['ques' => '', 'answer' => ''];
                                            $serial = $index + 1;
                                            $faqSlug = $faq->slug ?? 'faq-' . $faq->id;
                                        @endphp

                                        <tr>
                                            <td class="bg-color py-1">{{ $serial }}</td>
                                            <td class="bg-color py-1">
                                                {{ $faq->category ? $faq->category->name : 'Uncategorized' }}
                                            </td>
                                            <td class="bg-color py-1">{{ $item['ques'] ?? '' }}</td>
                                            <td class="bg-color py-1">{{ Str::limit($item['answer'] ?? '', 60) }}</td>
                                            <td class="bg-color py-1">
                                                <label class="switch">
                                                    @role('visitor')
                                                        <a class="visitorMessage">
                                                        @else
                                                            <a href="{{ route('web.faq.status.toggle', $faq->id) }}">
                                                            @endrole
                                                            <input type="checkbox"
                                                                {{ $faq->status == 'active' ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </a>
                                                </label>
                                            </td>
                                            <td class="bg-color p-1">
                                                <a href="{{ route('web.faq.edit', $faqSlug) }}"
                                                    class="btn btn-sm btn-primary mb-1">
                                                    <i class="far fa-edit"></i>
                                                </a>

                                                <a href="{{ route('web.faq.delete', $faqSlug) }}"
                                                    class="btn btn-sm btn-danger delete-confirm mb-1">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                {{ __('No FAQs found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
