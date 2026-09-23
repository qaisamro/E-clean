@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="card-title">{{ __('All Variants') }}</h2>
                            </div>

                            <div class="col-6 position-relative">
                                <button data-toggle="modal" data-target="#addNew" class="position-absolute btn btn-primary"
                                    style="right: 1em">
                                    {{ __('Add Variant') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table
                                class="table table-bordered table-striped verticle-middle table-responsive-sm {{ session()->get('local') }}"
                                id="myTable">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('Name') . ' ' . __('of') . ' ' . __('English') }}</th>
                                        <th scope="col">{{ __('Name') . ' ' . __('of') . ' ' . __('Arabic') }}</th>
                                        <th scope="col">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variants as $variant)
                                        <tr>
                                            <td>{{ $variant->name }}</td>
                                            <td>{{ $variant->name_bn }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#update{{ $variant->id }}">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                    <div class="modal fade" id="update{{ $variant->id }}">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h2 class="modal-title">
                                                                        {{ __('Edit') . ' ' . __('Variant') }}</h2>
                                                                    <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <form
                                                                    action="{{ route('variant.update', $variant->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <label>{{ __('Variant') . ' ' . __('Name') . ' ' . __('English') }}</label>
                                                                            <input type="text" name="name"
                                                                                class="form-control"
                                                                                value="{{ old('name') ?? $variant->name }}">
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label>{{ __('Variant') . ' ' . __('Name') . ' ' . __('Arabic') }}</label>
                                                                            <input type="text" name="name_bn"
                                                                                class="form-control"
                                                                                value="{{ old('name_bn') ?? $variant->name_bn }}"
                                                                                placeholder="اسم المتغير">
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label>{{ __('Position') }}</label>
                                                                            <input type="text" name="position"
                                                                                class="form-control"
                                                                                value="{{ old('position') ?? $variant->position }}"
                                                                                placeholder="{{ __('Position') }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">{{ __('Close') }}</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">{{ __('Save_changes') }}</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <a href="{{ route('variant.products', $variant->id) }}"
                                                        class="btn btn-info">{{ __('Products') }}</a>

                                                <form action="{{ route('variant.delete', $variant->id) }}" method="POST" style="display:inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger delete-confirm"><i class="fas fa-trash"></i></button>
                                                    </form>
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

        <div class="modal fade" id="addNew">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">{{ __('Add Variant') }}</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('variant.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label>{{ __('Variant') . ' ' . __('Name') . ' ' . __('English') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                                    placeholder="{{ __('Variant') . ' ' . __('Name') . ' ' . __('English') }}">
                            </div>

                            <div class="mb-3">
                                <label>{{ __('Variant') . ' ' . __('Name') . ' ' . __('Arabic') }}</label>
                                <input type="text" name="name_bn" class="form-control" value="{{ old('name_bn') }}"
                                    placeholder="{{ __('Variant') . ' ' . __('Name') . ' ' . __('Arabic') }}">
                            </div>

                            <div class="mb-3">
                                <label>{{ __('Position') }}</label>
                                <input type="text" name="position" class="form-control" value="{{ old('position') }}"
                                    placeholder="{{ __('Position') }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
@push('scripts')
<script>
    @if($errors->any())
        $(document).ready(function(){
            $('#addNew').modal('show');
        });
    @endif
</script>
@endpush
@endsection
