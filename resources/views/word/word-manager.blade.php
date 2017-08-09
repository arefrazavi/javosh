@extends('layouts.master-admin')

@section('title', 'Word Manager')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div>
                <section class="section">
                    <form class="form-inline" enctype="multipart/form-data"
                          action="{{ route('WordController.storeWords') }}" method="post">
                        {!! csrf_field() !!}
                        <input class="btn btn-warning" type="submit" value="Store Words">
                    </form>
                </section>
                <section class="section">
                    <form class="form-inline" enctype="multipart/form-data"
                          action="{{ route('WordController.cleanWords') }}"
                          method="post">
                        {!! csrf_field() !!}
                        <input class="btn btn-info" type="submit" value="Clean Words">
                    </form>
                </section>

            </div>
        </div>
        <div class="col-lg-12">
            @if (session('results'))
                @if(!empty(session('results')['errors']))
                    @foreach(session('results')['errors'] as $error)
                        <div class="alert alert-danger">
                            <ul>
                                {{ $error }}
                            </ul>
                        </div>
                    @endforeach
                @endif
                @if(!empty(session('results')['success']))
                    @foreach(session('results')['success'] as $success)
                        @if($success[0] == 0)
                            <div class="alert alert-warning">
                                <ul>
                                    {{ $success[1] }}
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <ul>
                                    {{ $success[1] }}
                                </ul>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endif
        </div>
    </div>
@endsection