@extends('layout')

@section('content')
    <section class="content-header">
        <h1>Device Section<small>View {{ Str::singular($deviceSection->title) }}</small></h1>

        <ol class="breadcrumb">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><span>Device Sections</span></li>
            <li><a href="{{ route('device.index', $deviceSection->id) }}"><span>{{ $deviceSection->title }}</span></a></li>
            <li class="active"><span>View {{ Str::singular($deviceSection->title) }}</span></li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">{{ Str::singular($deviceSection->title) }} Details</h3>
            </div>

            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Field Name</th>
                            <th>Field Type</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach ($deviceSection->fields as $field)
                        <tr>
                            <td>{{ $field->getName() }}</td>
                            <td>{{ $field->getType() }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @can('owner', $deviceSection)
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Shared with</h3>
                </div>

                <div class="box-body">
                    <ul>
                        @forelse ($deviceSection->sharedWith() as $share)
                            <li>
                                {!! $share->present()->sharedWith !!}

                                ({!! $share->present()->grantThrough !!})
                            </li>
                        @empty
                            <li>Device Section is not shared</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endcan

        <a href="{{ route('device-section.index') }}" class="btn btn-primary">Go Back</a>
    </section>
@stop
