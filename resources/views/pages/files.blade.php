@extends('layouts.app')

@php
use App\Models\User;
@endphp

@section('head.styles')
<style>
    .table-bordered>:not(caption)>*>:first-child,
    .table-bordered>:not(caption)>*>:last-child,
    .table-bordered>:not(caption)>:last-child {
        border-left-width: 0;
        border-bottom-width: 0;
        border-right-width: 0;
    }

    .table > tbody > tr:last-child > td:first-child {
        border-bottom-left-radius: .25rem;
    }

    .table > tbody > tr:last-child > td:last-child {
        border-bottom-right-radius: .25rem;
    }
    
    .file-icon {
        height: 32px;
        width: 32px;
    }
</style>
@endsection

@section('head.scripts')
<script type="text/javascript" src="{{ asset('js/clipboard.min.js') }}"></script>
@endsection

@section('content')
@include('components.navbar')

@include('components.toast')

<div class="container my-3">
    <div class="card text-justify">
        <div class="card-header p-2">
            <div class="row">
                <div class="col-sm d-flex justify-content-start">
                    <form class="row row-cols-lg-auto g-3 align-items-center">
                        <label class="visually-hidden" for="inlineSearch">Search...</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="inlineSearch" name="q" value="{{ request('q') }}" placeholder="Search...">
                            <button type="submit" class="btn btn-outline-secondary px-2"><i data-feather="search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col-sm d-flex justify-content-center">
                    {{ $files->appends(request()->input())->links('pagination.files') }}
                </div>
                <div class="col-sm d-flex justify-content-end">
                    <div class="btn-group " role="group" aria-label="Button group with nested dropdown">                      
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Sort by...
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <li><a class="dropdown-item" href="{{ route('files', ['order' => $order, 'sort' => 'created_at']) }}">Date</a></li>
                                <li><a class="dropdown-item" href="{{ route('files', ['order' => $order, 'sort' => 'media_size']) }}">Size</a></li>
                                <li><a class="dropdown-item" href="{{ route('files', ['order' => $order, 'sort' => 'media_name']) }}">Name</a></li>
                            </ul>
                        </div>
                        <a class="btn btn-outline-secondary px-2" href="{{ route('files', ['order' => $order == 'desc' ? 'asc' : 'desc', 'sort' => $sort]) }}">
                            @if($order == 'desc')
                                <i data-feather="trending-up"></i>
                            @else
                                <i data-feather="trending-down"></i>
                            @endif
                            
                        </a>
                    </div>
                    {{-- <button class="btn btn-outline-danger px-2 ms-2"><i data-feather="trash"></i></button> --}}
                </div>
            </div>
        </div>
        <div class="card-body p-0" style="margin: -1px;">
            @if($files->isEmpty())
                <p class="my-3 text-muted text-center">No media found.</p>
            @else
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                    <tr>
                        <th scope="col">Preview</th>
                        <th scope="col">Name</th>
                        <th scope="col">Size</th>
                        <th scope="col">Owner</th>
                        <th scope="col">Visible</th>
                        <th scope="col" colspan="2">Date</th>
                    </tr>
                    </thead>
                    <tbody id="files-table">
                        @foreach($files as $file)
                            @if(Files::exists($file))
                                <tr style="height: 92px;">
                                    <td class="text-center" style="max-width: 92px;">
                                        @if(Files::isDisplayableImage(Files::mimeType($file)))
                                            <img style="max-width: 75px" src="{{ $file->url() }}/raw" alt="">
                                        @else
                                            <p class="m-0"><i class="file-icon" data-feather="{{ $file->icon() }}"></i></p>
                                        @endif
                                    </td>
                                    <td style="max-width: 400px">{{ $file->media_name }}</td>
                                    <td>{{ Files::humanFileSize(Storage::disk('public')->size($file->path())) }}</td>
                                    <td>{{ $file->owner->username }}</td>
                                    <td class="text-center" data-state="{{ $file->media_code }}">
                                        @if($file->visible)
                                            <i class="text-success" data-feather="check-circle"></i>
                                        @else
                                            <i class="text-danger" data-feather="x-circle"></i>
                                        @endif
                                    </td>
                                    <td>{{ $file->created_at }}</td>
                                    <td scope="col">
                                        <div class="col d-flex text-center btn-group" data-id="{{ $file->media_code }}" data-name="{{ $file->media_name }}" data-visible="{{ $file->visible ? 1 : 0 }}">
                                            <a class="btn btn-sm px-1 btn-outline-secondary copy" title="Copy link" data-clipboard-text="{{ route('media.view', ['mediaCode' => $file->media_code]) }}">
                                                <i data-feather="link"></i>
                                            </a>
                                            <a class="btn btn-sm px-1 btn-outline-secondary" target="_blank" href="{{ route('media.view', ['mediaCode' => $file->media_code]) }}" title="Open">
                                                <span><i data-feather="external-link"></i></span>
                                            </a>
                                            <a class="btn btn-sm px-1 btn-outline-secondary" href="{{ route('media.download', ['mediaCode' => $file->media_code]) }}" title="Download">
                                                <i data-feather="download"></i>
                                            </a>
                                            <a class="btn btn-sm px-1 btn-outline-warning" data-action="toggle-visibility" title="{{ $file->visible ? "Hide" : "Show"}}">
                                                <i data-feather="{{ $file->visible ? "eye-off" : "eye"}}"></i>
                                            </a>
                                            <a class="btn btn-sm px-1 btn-outline-danger" data-action="delete" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
