@extends('layouts.admin')
@section('title','Documents')
@section('page-title','Application Documents')
@section('content')
<div style="display:flex;gap:12px;margin-bottom:20px;">
    <a href="{{ route('admin.loans.show',$application->id) }}" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Back to Application</a>
    <span style="font-family:monospace;font-weight:700;color:var(--blue);">{{ $application->application_id }}</span>
</div>
<div class="card">
    <div class="card-header"><div class="card-title">📁 Uploaded Documents ({{ $application->documents->count() }})</div></div>
    <div class="card-body">
        @if($application->documents->isEmpty())
            <div style="text-align:center;padding:40px;color:var(--gray-400);">No documents uploaded yet.</div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;">
            @foreach($application->documents as $doc)
            <div style="border:1.5px solid var(--gray-200);border-radius:12px;overflow:hidden;">
                <div style="background:var(--gray-50);padding:24px;text-align:center;font-size:40px;">
                    @if(in_array(pathinfo($doc->file_path,PATHINFO_EXTENSION),['jpg','jpeg','png']))
                        <img src="{{ $doc->url }}" style="width:100%;max-height:120px;object-fit:cover;border-radius:8px;">
                    @else
                        📄
                    @endif
                </div>
                <div style="padding:12px;">
                    <div style="font-weight:700;font-size:13px;margin-bottom:4px;">{{ ucwords(str_replace('_',' ',$doc->type)) }}</div>
                    <div style="font-size:12px;margin-bottom:10px;">
                        @if($doc->verification_status==='verified') <span class="badge badge-success">✅ Verified</span>
                        @elseif($doc->verification_status==='rejected') <span class="badge badge-danger">❌ Rejected</span>
                        @else <span class="badge badge-warning">⏳ Pending</span>
                        @endif
                    </div>
                    <a href="{{ $doc->url }}" target="_blank" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">View</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
