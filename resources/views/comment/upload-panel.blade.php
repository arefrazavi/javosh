@extends('layouts.master-admin')

@section('title', 'Upload Files')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div>
                <form class="form-inline" enctype="multipart/form-data" action="{{ route('CommentController.upload') }}"
                      method="post">
                    {!! csrf_field() !!}
                    <input class="form-control" type="file" name="files[]" multiple="multiple">
                    <input class="btn btn-info" type="submit" value="Upload">
                </form>
            </div>
            <div>
                <form class="form-inline" action="{{ route('SentenceController.storeSentences') }}" method="post">
                    {!! csrf_field() !!}
                    <input class="btn btn-info" type="submit" value="Store Sentences">
                </form>
            </div>
            <div>
                <form class="form-inline" action="{{ route('SentenceController.generateWord2VecInput') }}"
                      method="post">
                    {!! csrf_field() !!}
                    <input class="btn btn-info" type="submit" value="Generate Word2Vec Input">
                </form>
            </div>
            <div>
                <form class="form-inline" action="{{ route('WordController.computeWordsSentiment') }}" method="post">
                    {!! csrf_field() !!}
                    <input class="btn btn-info" type="submit" value="Compute words sentiment">
                </form>
            </div>
            <div>
                <form class="form-inline" action="{{ route('SentenceController.computeSentencesSentiment') }}"
                      method="post">
                    {!! csrf_field() !!}
                    <input class="btn btn-info" type="submit" value="Compute sentences sentiment">
                </form>
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
                            <div class="alert alert-success">
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