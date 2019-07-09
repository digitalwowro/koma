@extends('layout')

@section('content')
    <section class="content-header">
        <h1>IP Category<small>View {{ Str::singular($category->title) }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Devices</span></li>
            <li><a href="{{ route('device.index', $category->id) }}"><span>{{ $category->title }}</span></a></li>
            <li class="active"><span>View {{ Str::singular($category->title) }}</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Str::singular($category->title) }} Details</h3>
            </div>

            <div class="box-body">
                <strong>Name:</strong> {{ $category->title }}
            </div>
        </div>

        @can('owner', $category)
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Shared with</h3>
                </div>

                <div class="box-body">
                    <ul>
                        @forelse ($category->sharedWith() as $share)
                            <li>
                                <a href="{{ route('users.edit', $share->user_id) }}">{{ $share->user->name }}</a>

                                ({!! $share->present()->grantThrough !!})

                                {!! Form::open(['route' => ['ip-category.share', $category->id], 'method' => 'POST', 'style' => 'display: inline;']) !!}
                                {!! Form::hidden('user_id', $share->user_id) !!}

                                <a href="#" style="color: red;" onclick="$(this).closest('form').submit(); return false;">
                                    <i class="fa fa-times"></i>
                                    Revoke
                                </a>
                                {!! Form::close() !!}
                            </li>
                        @empty
                            <li>IP Category is not shared</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endcan

        <a href="{{ route('ip-category.index') }}" class="btn btn-primary">Go Back</a>
    </section>
@stop
